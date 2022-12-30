<?php

declare(strict_types=1);

namespace Arokettu\Torrent\FileSystem;

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

    /**
     * @deprecated use $this->total
     */
    public function getTotal(): int
    {
        trigger_deprecation(
            'arokettu/torrent-file',
            '3.2.0',
            '->total instead of ->getTotal()',
        );
        return $this->total;
    }

    /**
     * @deprecated use $this->done
     */
    public function getDone(): int
    {
        trigger_deprecation(
            'arokettu/torrent-file',
            '3.2.0',
            '->done instead of ->getDone()',
        );
        return $this->done;
    }

    /**
     * @deprecated use $this->fileName
     */
    public function getFileName(): string
    {
        trigger_deprecation(
            'arokettu/torrent-file',
            '3.2.0',
            '->fileName instead of ->getFileName()',
        );
        return $this->fileName;
    }
}
