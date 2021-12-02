<?php

declare(strict_types=1);

namespace SandFox\Torrent\FileSystem\V2;

use SandFox\Torrent\Exception\RuntimeException;
use SandFox\Torrent\FileSystem\FileData;
use SandFox\Torrent\Helpers\MathHelper;
use SplFileObject;
use Symfony\Component\Finder\SplFileInfo;

class MultipleFileData extends FileData
{
    private int $merkleTreePieceLevel;

    protected function init(): void
    {
        $this->merkleTreePieceLevel = MathHelper::log2i($this->pieceLength) - self::PIECE_LENGTH_MIN_LOG_2;
    }

    public function process(): array
    {
        $info = [
            'meta version'  => 2,
            'piece length'  => $this->pieceLength,
            'name'          => basename($this->path),
            'file tree'     => [],
        ];
        $layers = [];

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

        $doneSize = 0;

        // no need to sort, files will be sorted on encoding

        foreach ($filePaths as $filePath) {
            $fileRecord = &$info['file tree'];

            foreach ($filePath['explodedPath'] as $component) {
                $fileRecord[$component] ??= [];
                $fileRecord = &$fileRecord[$component];
            }

            [$record, $layer] = $this->buildFileRecord($filePath, $doneSize);

            $fileRecord[''] = $record;

            if ($layer !== null) {
                $layers[$record['pieces root']] = $layer;
            }

            $doneSize += $filePath['fileObject']->getSize();
            $this->reportProgress($totalSize, $doneSize, $filePath['relativePath']);
        }

        return [
            'info' => $info,
            'piece layers' => $layers,
        ];
    }

    private function buildFileRecord(array $filePath): array
    {
        $link = $this->detectSymlink($filePath['fullPath']);
        if ($link !== null) {
            return [
                [
                    'attr' => 'l',
                    'symlink path' => $link,
                ],
                null,
            ];
        }

        /** @var SplFileObject $file */
        $file = $filePath['fileObject'];

        $record = [
            'attr' => $this->getAttributes($filePath['fullPath']),
            'length' => $length = $file->getSize(),
        ];

        if ($length === 0) {
            return [$record, null];
        }

        $hashes = [];

        // generate 1st level of merkle tree
        $file->rewind();
        while (!$file->eof()) {
            $data = $file->fread(self::PIECE_LENGTH_MIN);
            if ($data === '') {
                continue; // trigger eof
            }
            if ($data === false) {
                // @codeCoverageIgnoreStart
                throw new RuntimeException('Unable to read file ' . $filePath['fullPath']);
                // @codeCoverageIgnoreEnd
            }
            $hashes[] = hash('sha256', $data, true);
        }

        $record['pieces root'] = MathHelper::merkleTreeRootSha256($hashes);
        $layer = null;

        if ($length > $this->pieceLength) {
            $hashes = MathHelper::merkleTreeLevelSha256($hashes, $this->merkleTreePieceLevel);
            $layer = implode('', $hashes);
        }

        return [$record, $layer];
    }
}
