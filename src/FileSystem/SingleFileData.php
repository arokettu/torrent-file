<?php

namespace SandFoxMe\Torrent\FileSystem;

class SingleFileData extends FileData
{
    protected function process()
    {
        $data = [
            'piece length'  => $this->options['pieceLength'],
            'name'          => basename($this->path),
            'length'        => filesize($this->path),
        ];

        $this->reportProgress($data['length'], 0, $data['name']);

        if ($this->options['md5']) {
            $data['md5sum'] = md5_file($this->path);
        }

        $chunkSize = $this->options['pieceLength'];

        $chunkHashes = [];

        $file = fopen($this->path, 'r');

        while ($chunk = fread($file, $chunkSize)) {
            $chunkHashes []= $this->hashChunk($chunk);
            $this->reportProgress($data['length'], ftell($file), $data['name']);
        }

        $data['pieces'] = implode($chunkHashes);

        $this->data = $data;
    }
}
