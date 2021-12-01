<?php

declare(strict_types=1);

namespace SandFox\Torrent\FileSystem;

final class FileDataProgressEvent
{
    /**
     * @param int $total Total size
     * @param int $done Processed size
     * @param string $fileName Current file name
     */
    public function __construct(
        public readonly int $total,
        public readonly int $done,
        public readonly string $fileName,
    ) {
    }

    // todo: deprecate in 3.2
    public function getTotal(): int
    {
        return $this->total;
    }

    // todo: deprecate in 3.2
    public function getDone(): int
    {
        return $this->done;
    }

    // todo: deprecate in 3.2
    public function getFileName(): string
    {
        return $this->fileName;
    }
}
