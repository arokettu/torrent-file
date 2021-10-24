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

        $data = [
            'piece length'  => $this->pieceLength,
            'name'          => $file->getBasename(),
            'length'        => $file->getSize(),
        ];

        $this->reportProgress($data['length'], 0, $data['name']);

        if ($this->md5sum) {
            $data['md5sum'] = md5_file($this->path);
        }

        $chunkSize = $this->pieceLength;

        $chunkHashes = [];

        while ($chunk = $file->fread($chunkSize)) {
            $chunkHashes[] = $this->hashChunk($chunk);
            $this->reportProgress($data['length'], $file->ftell(), $data['name']);
        }

        $data['pieces'] = implode($chunkHashes);

        return $data;
    }
}
