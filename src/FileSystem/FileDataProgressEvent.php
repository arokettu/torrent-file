<?php

declare(strict_types=1);

namespace SandFox\Torrent\FileSystem;

class FileDataProgressEvent
{
    private int $total;
    private int $done;
    private string $fileName;

    /**
     * @param int $total Total size
     * @param int $done Processed size
     * @param string $fileName Current file name
     */
    public function __construct(int $total, int $done, string $fileName)
    {
        $this->total    = $total;
        $this->done     = $done;
        $this->fileName = $fileName;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getDone(): int
    {
        return $this->done;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}
