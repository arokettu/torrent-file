<?php

declare(strict_types=1);

namespace SandFox\Torrent\FileSystem\V1;

use SandFox\Torrent\FileSystem\FileData;
use SplFileObject;
use Symfony\Component\Finder\Finder;

/**
 * @internal
 */
final class MultipleFileData extends FileData
{
    public function process(): array
    {
        $info = [
            'piece length'  => $this->pieceLength,
            'name' => basename($this->path),
        ];

        $finder = new Finder();

        // don't ignore files
        $finder->ignoreDotFiles(false);
        $finder->ignoreVCS(false);

        $filePaths = [];

        $totalSize = 0;

        foreach ($finder->files()->in($this->path) as $file) {
            $filePaths[] = [
                'fullPath'      => realpath($file->getPathname()),
                'relativePath'  => $file->getRelativePathname(),
                'explodedPath'  => explode(DIRECTORY_SEPARATOR, $file->getRelativePathname()),
            ];

            $totalSize += $file->getSize();
        }

        $this->reportProgress($totalSize, 0, $info['name']);

        // sort files by binary comparing exploded parts
        usort($filePaths, function ($path1, $path2): int {
            $exploded1 = $path1['explodedPath'];
            $exploded2 = $path2['explodedPath'];

            $partsCount = min(\count($exploded1), \count($exploded2));

            for ($i = 0; $i < $partsCount; ++$i) {
                $result = strcmp($exploded1[$i], $exploded2[$i]);

                if ($result !== 0) {
                    return $result;
                }
            }

            // @codeCoverageIgnoreStart
            throw new \LogicException(
                "You can't have two files with the same name: " .
                $path1['relativePath'] .
                ' and ' .
                $path2['relativePath']
            );
            // @codeCoverageIgnoreEnd
        });

        // now process files
        $files = [];
        $chunkHashes = [];

        $chunkSize = $this->pieceLength;
        $currentChunk = '';

        $doneSize = 0;

        foreach ($filePaths as $filePath) {
            $file = new SplFileObject($filePath['fullPath']);

            // create file metadata
            $fileData = [
                'path'      => $filePath['explodedPath'],
                'length'    => $file->getSize(),
                'attr'      => $this->getAttributes($filePath['fullPath']),
                'sha1'      => sha1_file($filePath['fullPath'], true),
            ];

            $files[] = $fileData;

            // create chunk hashes
            $chunkReadSize = $chunkSize - \strlen($currentChunk);

            while ($partialChunk = $file->fread($chunkReadSize)) {
                $currentChunk .= $partialChunk;

                if (\strlen($currentChunk) < $chunkSize) {
                    break; // add next file to the chunk
                }

                // we have complete chunk here
                $chunkHashes[] = $this->hashChunk($currentChunk);

                $doneSize += \strlen($currentChunk);
                $this->reportProgress($totalSize, $doneSize, $filePath['relativePath']);

                $currentChunk = ''; // start new chunk
                $chunkReadSize = $chunkSize; // reset read length
            }
        }

        // hash last chunk
        if (\strlen($currentChunk) > 0) {
            $chunkHashes[] = $this->hashChunk($currentChunk);

            $doneSize += \strlen($currentChunk);

            $this->reportProgress($totalSize, $doneSize, $info['name']);
        }

        $info['files']  = $files;
        $info['pieces'] = implode($chunkHashes);

        return ['info' => $info];
    }
}
