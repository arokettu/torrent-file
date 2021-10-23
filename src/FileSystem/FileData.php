<?php

declare(strict_types=1);

namespace SandFox\Torrent\FileSystem;

use Psr\EventDispatcher\EventDispatcherInterface;
use SandFox\Torrent\Exception\InvalidArgumentException;
use SandFox\Torrent\Exception\PathNotFoundException;
use Symfony\Component\Filesystem\Path;

/**
 * @internal
 */
abstract class FileData
{
    private const PIECE_LENGTH_MIN = 16 * 1024;

    public static function forPath(
        string $path,
        ?EventDispatcherInterface $eventDispatcher,
        int $pieceLength,
        int $pieceAlign,
        bool $detectExec,
        bool $detectSymlinks,
    ): self {
        $params = [
            realpath($path),
            $eventDispatcher,
            $pieceLength,
            $pieceAlign,
            $detectExec,
            $detectSymlinks,
        ];

        return match (true) {
            is_file($path)
                => new V1\SingleFileData(...$params),
            is_dir($path)
                => new V1\MultipleFileData(...$params),
            default
                => throw new PathNotFoundException(
                    "Path '{$path}' doesn't exist or is not a regular file or a directory"
                ),
        };
    }

    protected function __construct(
        protected string $path,
        private ?EventDispatcherInterface $eventDispatcher,
        protected int $pieceLength,
        protected int $pieceAlign,
        protected bool $detectExec,
        protected bool $detectSymlinks,
    ) {
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
        $this->eventDispatcher?->dispatch(new FileDataProgressEvent($total, $done, $fileName));
    }

    protected function detectSymlink(string $path): ?array
    {
        if (!$this->detectSymlinks) {
            return null;
        }

        if (!is_link($path)) {
            return null;
        }

        // peel one layer of a link

        $link = readlink($path);

        if (Path::isRelative($link)) {
            $link = Path::makeAbsolute($link, Path::getDirectory($path));
        }

        // leading beyond the torrent root
        if (!str_starts_with($link, $this->path)) {
            return null;
        }

        return array_values(
            array_filter(
                explode('/', substr($link, \strlen($this->path))),
                fn ($s) => $s !== ''
            )
        );
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
