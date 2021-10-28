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
    private const PIECE_LENGTH_MIN = 16 * 1024;

    protected string $path;
    protected int $pieceLength;
    protected bool $detectExec;
    protected bool $detectSymlinks;

    private ?EventDispatcherInterface $eventDispatcher;

    public static function forPath(
        string $path,
        ?EventDispatcherInterface $eventDispatcher,
        int $pieceLength,
        bool $detectExec,
        bool $detectSymlinks
    ): self {
        $params = [
            realpath($path),
            $eventDispatcher,
            $pieceLength,
            $detectExec,
            $detectSymlinks,
        ];

        if (is_file($path)) {
            return new V1\SingleFileData(...$params);
        }
        if (is_dir($path)) {
            return new V1\MultipleFileData(...$params);
        }

        throw new PathNotFoundException("Path '{$path}' doesn't exist or is not a regular file or a directory");
    }

    protected function __construct(
        string $path,
        ?EventDispatcherInterface $eventDispatcher,
        int $pieceLength,
        bool $detectExec,
        bool $detectSymlinks
    ) {
        $this->path = $path;
        $this->eventDispatcher = $eventDispatcher;
        $this->pieceLength = $pieceLength;
        $this->detectExec = $detectExec;
        $this->detectSymlinks = $detectSymlinks;

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

    protected function getAttributes(string $path): ?string
    {
        $attr = null;

        if (is_executable($path)) {
            $attr .= 'x';
        }

        return $attr;
    }
}
