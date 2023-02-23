<?php

declare(strict_types=1);

namespace Arokettu\Torrent\FileSystem;

use Arokettu\Bencode\Bencode;
use Arokettu\Torrent\Exception\InvalidArgumentException;
use Arokettu\Torrent\Exception\PathNotFoundException;
use Arokettu\Torrent\Helpers\MathHelper;
use Arokettu\Torrent\MetaVersion;
use Psr\EventDispatcher\EventDispatcherInterface;
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
        MetaVersion $version,
        int $pieceLength,
        int $pieceAlign,
        bool $detectExec,
        bool $detectSymlinks,
        bool $forceMultifile,
    ): self {
        $params = [
            realpath($path),
            $eventDispatcher,
            $pieceLength,
            $pieceAlign,
            $detectExec,
            $detectSymlinks,
            $forceMultifile,
        ];

        $isFile = is_file($path);
        $isDir  = is_dir($path);

        if (!$isFile && !$isDir) {
            throw new PathNotFoundException("Path '{$path}' doesn't exist or is not a regular file or a directory");
        }

        return match ($version) {
            MetaVersion::V1
                => ($isFile && !$forceMultifile) ?
                    new V1\SingleFileData(...$params) :
                    new V1\MultipleFileData(...$params),
            MetaVersion::V2
                => new V2\MultipleFileData(...$params),
            MetaVersion::HybridV1V2
                => new HybridV1V2\MultipleFileData(...$params),
        };
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

    public function getBencoded(): string
    {
        return Bencode::encode($this->process());
    }

    protected function init(): void
    {
    }

    abstract protected function process(): array;

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

        if ($this->detectExec && is_executable($path)) {
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
