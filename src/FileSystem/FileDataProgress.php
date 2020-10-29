<?php

declare(strict_types=1);

namespace SandFox\Torrent\FileSystem;

class FileDataProgress
{
    /**
     * @var int Total size
     */
    private $total = 0;
    /**
     * @var int Processed size
     */
    private $done = 0;
    /**
     * @var string Current file name
     */
    private $fileName = '';
    /**
     * @var callable
     */
    private $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @return int
     */
    public function getDone(): int
    {
        return $this->done;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }

    /**
     * @param int $total
     * @param int $done
     * @param string $fileName
     */
    public function setCurrentData(int $total, int $done, string $fileName)
    {
        $this->total    = $total;
        $this->done     = $done;
        $this->fileName = $fileName;

        call_user_func($this->callback, $this);
    }
}
