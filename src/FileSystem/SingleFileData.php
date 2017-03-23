<?php

namespace SandFoxMe\Torrent\FileSystem;

class SingleFileData extends FileData
{
    protected function process()
    {
        $file = new \SplFileObject($this->path);

        $data = [
            'piece length'  => $this->options['pieceLength'],
            'name'          => $file->getBasename(),
            'length'        => $file->getSize(),
        ];

        $this->reportProgress($data['length'], 0, $data['name']);

        if ($this->options['md5']) {
            $data['md5sum'] = md5_file($this->path);
        }

        $chunkSize = $this->options['pieceLength'];

        $chunkHashes = [];

        while ($chunk = $file->fread($chunkSize)) {
            $chunkHashes []= $this->hashChunk($chunk);
            $this->reportProgress($data['length'], $file->ftell(), $data['name']);
        }

        $data['pieces'] = implode($chunkHashes);

        $this->data = $data;
    }
}
