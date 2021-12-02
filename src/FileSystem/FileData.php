<?php

declare(strict_types=1);

namespace SandFox\Torrent\FileSystem;

use Psr\EventDispatcher\EventDispatcherInterface;
use SandFox\Torrent\Exception\InvalidArgumentException;
use SandFox\Torrent\Exception\PathNotFoundException;
use SandFox\Torrent\Helpers\MathHelper;
use SandFox\Torrent\MetaVersion;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Finder\Finder;

/**
 * @internal
 */
abstract class FileData
{
    protected const PIECE_LENGTH_MIN = 16 * 1024;
    protected const PIECE_LENGTH_MIN_LOG_2 = 14; // log2(16) + log2(1024) = 4 + 10

    public static function forPath(
        string $path,
        ?EventDispatcherInterface $eventDispatcher,
        string $version,
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

        $isFile = is_file($path);
        $isDir  = is_dir($path);

        if (!$isFile && !$isDir) {
            throw new PathNotFoundException("Path '{$path}' doesn't exist or is not a regular file or a directory");
        }

        switch ($version) {
            case MetaVersion::V1:
                return $isFile ? new V1\SingleFileData(...$params) : new V1\MultipleFileData(...$params);

            case MetaVersion::V2:
                return new V2\MultipleFileData(...$params);

            case MetaVersion::HybridV1V2:
                return new HybridV1V2\MultipleFileData(...$params);

            default:
                // @codeCoverageIgnoreStart
                throw new InvalidArgumentException("Unknown torrent metadata version: " . $version);
                // @codeCoverageIgnoreEnd
        }
    }

    protected function __construct(
        protected string $path,
        protected ?EventDispatcherInterface $eventDispatcher,
        protected int $pieceLength,
        protected int $pieceAlign,
        protected bool $detectExec,
        protected bool $detectSymlinks,
    ) {
        if ($pieceLength < self::PIECE_LENGTH_MIN || !MathHelper::isPow2($pieceLength)) {
            throw new InvalidArgumentException(
                'pieceLength must be a power of 2 and at least ' . self::PIECE_LENGTH_MIN
            );
        }

        $this->init();
    }

    protected function init(): void
    {
    }

    abstract public function process(): array;

    protected function hashChunkV1(string $chunk): string
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

    protected function finder(): Finder
    {
        $finder = new Finder();

        // don't ignore files
        $finder->ignoreDotFiles(false);
        $finder->ignoreVCS(false);

        return $finder;
    }
}
