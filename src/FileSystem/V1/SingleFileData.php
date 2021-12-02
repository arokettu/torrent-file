<?php

declare(strict_types=1);

namespace SandFox\Torrent\FileSystem\V1;

use SandFox\Torrent\FileSystem\FileData;
use SplFileObject;

/**
 * @internal
 */
final class SingleFileData extends FileData
{
    public function process(): array
    {
        $file = new SplFileObject($this->path);

        $info = [
            'piece length'  => $this->pieceLength,
            'name'          => $file->getBasename(),
            'length'        => $file->getSize(),
            'attr'          => $this->getAttributes($this->path),
            'sha1'          => sha1_file($this->path, true),
        ];

        $this->reportProgress($info['length'], 0, $info['name']);

        $chunkSize = $this->pieceLength;

        $chunkHashes = [];

        while ($chunk = $file->fread($chunkSize)) {
            $chunkHashes[] = $this->hashChunkV1($chunk);
            $this->reportProgress($info['length'], $file->ftell(), $info['name']);
        }

        $info['pieces'] = implode($chunkHashes);

        return ['info' => $info];
    }
}
