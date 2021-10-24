<?php

declare(strict_types=1);

namespace SandFox\Torrent\FileSystem;

use Psr\EventDispatcher\EventDispatcherInterface;
use SandFox\Torrent\Exception\InvalidArgumentException;
use SandFox\Torrent\Exception\PathNotFoundException;

/**
 * @internal
 */
abstract class FileData
{
    protected string $path;
    protected int $pieceLength;
    protected bool $md5sum;

    private ?EventDispatcherInterface $eventDispatcher;

    private const PIECE_LENGTH_MIN = 16 * 1024;

    public static function forPath(
        string $path,
        ?EventDispatcherInterface $eventDispatcher,
        int $pieceLength,
        bool $md5sum
    ): self {
        if (is_file($path)) {
            return new SingleFileData(realpath($path), $eventDispatcher, $pieceLength, $md5sum);
        }
        if (is_dir($path)) {
            return new MultipleFileData(realpath($path), $eventDispatcher, $pieceLength, $md5sum);
        }

        throw new PathNotFoundException("Path '{$path}' doesn't exist or is not a regular file or a directory");
    }

    protected function __construct(
        string $path,
        ?EventDispatcherInterface $eventDispatcher,
        int $pieceLength,
        bool $md5sum
    ) {
        $this->path = $path;
        $this->eventDispatcher = $eventDispatcher;
        $this->pieceLength = $pieceLength;
        $this->md5sum = $md5sum;

        if ($pieceLength < self::PIECE_LENGTH_MIN || ($pieceLength & ($pieceLength - 1)) !== 0) {
            throw new InvalidArgumentException(
                'pieceLength must be a power of 2 and at least ' . self::PIECE_LENGTH_MIN
            );
        }
    }

    abstract public function process(): array;

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
