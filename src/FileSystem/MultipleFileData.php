<?php

namespace SandFoxMe\Torrent\FileSystem;

use Symfony\Component\Finder\Finder;

class MultipleFileData extends FileData
{
    protected function process()
    {
        $data = [
            'piece length'  => $this->options['pieceLength'],
            'name' => basename($this->path),
        ];

        $finder = new Finder();

        // don't ignore files
        $finder->ignoreDotFiles(false);
        $finder->ignoreVCS(false);

        $filePaths = [];

        $totalSize = 0;

        foreach ($finder->files()->in($this->path) as $file) {
            /** @var \Symfony\Component\Finder\SplFileInfo $file */
            $filePaths []= [
                'fullPath'      => realpath($file->getPathname()),
                'relativePath'  => $file->getRelativePathname(),
                'explodedPath'  => explode(DIRECTORY_SEPARATOR, $file->getRelativePathname()),
            ];

            $totalSize += $file->getSize();
        }

        var_dump($this->options['sortFiles']);

        $this->reportProgress($totalSize, 0, $data['name']);

        if ($this->options['sortFiles']) {
            // sort files by binary comparing exploded parts
            usort($filePaths, function ($path1, $path2) {
                $exploded1 = $path1['explodedPath'];
                $exploded2 = $path2['explodedPath'];

                $partsCount = min(count($exploded1), count($exploded2));

                for ($i = 0; $i < $partsCount; $i++) {
                    $result = strcmp($exploded1[$i], $exploded2[$i]);

                    if ($result !== 0) {
                        return $result;
                    }
                }

                return 0; // this however should never happen
            });
        }

        // now process files
        $files  = [];
        $chunkHashes = [];

        $chunkSize = $this->options['pieceLength'];
        $currentChunk = '';

        $doneSize = 0;

        foreach ($filePaths as $filePath) {
            $file = new \SplFileObject($filePath['fullPath']);

            // create file metadata
            $fileData = [
                'path'      => $filePath['explodedPath'],
                'length'    => $file->getSize(),
            ];

            if ($this->options['md5sum']) {
                $fileData['md5sum'] = md5_file($filePath['fullPath']);
            }

            $files []= $fileData;

            // create chunk hashes
            $chunkReadSize = $chunkSize - strlen($currentChunk);

            while ($partialChunk = $file->fread($chunkReadSize)) {
                $currentChunk .= $partialChunk;

                if (strlen($currentChunk) < $chunkSize) {
                    break; // add next file to the chunk
                }

                // we have complete chunk here
                $chunkHashes []= $this->hashChunk($currentChunk);

                $doneSize += strlen($currentChunk);
                $this->reportProgress($totalSize, $doneSize, $filePath['relativePath']);

                $currentChunk = ''; // start new chunk
                $chunkReadSize = $chunkSize; // reset read length
            }
        }

        // hash last chunk
        if (strlen($currentChunk) > 0) {
            $chunkHashes []= $this->hashChunk($currentChunk);

            $doneSize += strlen($currentChunk);

            $this->reportProgress($totalSize, $doneSize, $data['name']);
        }

        $data['files']  = $files;
        $data['pieces'] = implode($chunkHashes);

        $this->data = $data;
    }
}
