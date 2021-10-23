<?php

declare(strict_types=1);

namespace SandFox\Torrent\FileSystem;

use Psr\EventDispatcher\EventDispatcherInterface;
use SandFox\Torrent\Exception\PathNotFoundException;

/**
 * @internal
 */
abstract class FileData
{
    protected array $data;
    protected string $path;
    protected array $options;

    private ?EventDispatcherInterface $eventDispatcher = null;

    public const DEFAULT_OPTIONS = [
        'pieceLength'   => 512 * 1024, // 512 KB
        'md5sum'        => false,
    ];

    public static function forPath(string $path, array $options = []): self
    {
        // @codeCoverageIgnoreStart
        if (isset($options['sortFiles'])) {
            trigger_deprecation(
                'sandfoxme/torrent-file',
                '2.2',
                'sortFiles option is deprecated. Files are always sorted now',
            );
        }
        // @codeCoverageIgnoreEnd

        if (is_file($path)) {
            return new SingleFileData(realpath($path), $options);
        }
        if (is_dir($path)) {
            return new MultipleFileData(realpath($path), $options);
        }

        throw new PathNotFoundException("Path '{$path}' doesn't exist or is not a regular file or a directory");
    }

    protected function __construct(string $path, array $options = [])
    {
        $this->path = $path;
        $this->options = array_merge(self::DEFAULT_OPTIONS, $options);
    }

    public function generateData(?EventDispatcherInterface $eventDispatcher = null): void
    {
        $this->eventDispatcher = $eventDispatcher;

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
        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(new FileDataProgressEvent($total, $done, $fileName));
        }
    }
}
