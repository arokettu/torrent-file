<?php

namespace SandFox\Torrent\FileSystem;

use SandFox\Torrent\Exception\PathNotFoundException;

abstract class FileData
{
    protected $data;
    protected $path;
    protected $options;
    /**
     * @var FileDataProgress
     */
    protected $progress;

    public const DEFAULT_OPTIONS = [
        'pieceLength'   => 512 * 1024, // 512 KB
        'md5sum'        => false,
        'sortFiles'     => true,
    ];

    public static function forPath(string $path, array $options = []): self
    {
        $path = realpath($path);

        if (is_file($path)) {
            return new SingleFileData($path, $options);
        }
        if (is_dir($path)) {
            return new MultipleFileData($path, $options);
        }

        throw new PathNotFoundException("Path '{$path}' doesn't exist or is not regular file or directory");
    }

    protected function __construct(string $path, array $options = [])
    {
        $this->path = $path;
        $this->options = array_merge(self::DEFAULT_OPTIONS, $options);
    }

    public function generateData(?FileDataProgress $progress = null): void
    {
        $this->progress = $progress;

        $this->process();
    }

    public function getData(): array
    {
        return $this->data;
    }

    abstract protected function process(): void;

    protected function hashChunk(string $chunk): string
    {
        return sha1($chunk, true);
    }

    protected function reportProgress(int $total, int $done, string $fileName): void
    {
        if ($this->progress) {
            $this->progress->setCurrentData($total, $done, $fileName);
        }
    }
}
