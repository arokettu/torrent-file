<?php

declare(strict_types=1);

namespace Arokettu\Torrent\FileSystem\V1;

use Arokettu\Torrent\FileSystem\FileData;
use SplFileObject;
use Symfony\Component\Finder\SplFileInfo;

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

        $filePaths = [];
        $totalSize = 0;

        $files = is_dir($this->path) ?
            $this->finder()->files()->in($this->path) :
            [new SplFileInfo($this->path, '.', basename($this->path))];

        foreach ($files as $file) {
            $filePaths[] = [
                'fullPath'      => $file->getPathname(),
                'relativePath'  => $file->getRelativePathname(),
                'explodedPath'  => explode(DIRECTORY_SEPARATOR, $file->getRelativePathname()),
                'fileObject'    => new SplFileObject($file->getPathname()),
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

        foreach ($filePaths as $fileKey => $filePath) {
            /** @var SplFileObject $file */
            $file = $filePath['fileObject'];

            $link = $this->detectSymlink($filePath['fullPath']);
            if ($link !== null) {
                $files[] = [
                    'path'          => $filePath['explodedPath'],
                    'attr'          => 'l',
                    'length'        => 0, // compatibility
                    'symlink path'  => $link,
                ];
                continue;
            }

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
                $currentChunkLenWithoutPadding = \strlen($currentChunk);

                // incomplete chunk
                if (\strlen($currentChunk) < $chunkSize) {
                    $nextFilePath = $filePaths[$fileKey + 1] ?? null;
                    if ($nextFilePath === null) {
                        break; // last file, no need for padding
                    }

                    /** @var SplFileObject $fileNext */
                    $fileNext = $nextFilePath['fileObject'];
                    if (
                        $file->getSize() < $this->pieceAlign && // no need to pad this file
                        $fileNext->getSize() < $this->pieceAlign // no need to pad next file
                    ) {
                        break; // no need for padding: read the next file for a partial chunk
                    }

                    // add pad 'file'
                    $padSize = $chunkSize - \strlen($currentChunk);
                    $padFileData = [
                        'attr'      => 'p',
                        'length'    => $padSize,
                        'path'      => ['.pad', \strval($padSize)]
                    ];
                    $files[] = $padFileData;

                    // complete the chunk
                    $currentChunk = str_pad($currentChunk, $chunkSize, "\0", \STR_PAD_RIGHT);

                    // fall through to the complete chunk logic
                }

                // we have complete chunk here
                $chunkHashes[] = $this->hashChunkV1($currentChunk);

                $doneSize += $currentChunkLenWithoutPadding;
                $this->reportProgress($totalSize, $doneSize, $filePath['relativePath']);

                $currentChunk = ''; // start new chunk
                $chunkReadSize = $chunkSize; // reset read length
            }
        }

        // hash last chunk
        if (\strlen($currentChunk) > 0) {
            $chunkHashes[] = $this->hashChunkV1($currentChunk);

            $doneSize += \strlen($currentChunk);

            $this->reportProgress($totalSize, $doneSize, $info['name']);
        }

        $info['files']  = $files;
        $info['pieces'] = implode('', $chunkHashes);

        return ['info' => $info];
    }
}
