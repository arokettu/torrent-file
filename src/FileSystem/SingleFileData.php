<?php

declare(strict_types=1);

namespace SandFox\Torrent\FileSystem;

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
        ];

        $this->reportProgress($info['length'], 0, $info['name']);

        if ($this->md5sum) {
            $info['md5sum'] = md5_file($this->path);
        }

        $chunkSize = $this->pieceLength;

        $chunkHashes = [];

        while ($chunk = $file->fread($chunkSize)) {
            $chunkHashes[] = $this->hashChunk($chunk);
            $this->reportProgress($info['length'], $file->ftell(), $info['name']);
        }

        $info['pieces'] = implode($chunkHashes);

        return ['info' => $info];
    }
}
