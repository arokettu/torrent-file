<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile\V2;

use Arokettu\Torrent\DataTypes\Internal\DictObject;
use Arokettu\Torrent\Exception\RuntimeException;
use Arokettu\Torrent\TorrentFile\Common\Attributes;

/**
 * @implements \RecursiveIterator<string, File|FileTree>
 */
final class FileTree implements \RecursiveIterator, \Countable
{
    /** @var array<string, File|FileTree> */
    public readonly array $files;
    /** @var \ArrayIterator<string, File|FileTree> */
    private readonly \ArrayIterator $iterator;

    public function __construct(
        DictObject $files,
        public readonly array $path,
    ) {
        $this->parseList($files);
    }

    private function parseList(DictObject $files): void
    {
        $parsedFiles = [];

        foreach ($files as $key => $file) {
            if ($key === '') {
                continue;
            }

            $key = \strval($key); // counter array keys type cast

            if (isset($file['']['length'])) {
                if (\count($file) > 1) {
                    throw new RuntimeException('Invalid node: file cannot contain child files');
                }
                // file
                $data = $file[''];
                $attributes = new Attributes($data['attr'] ?? '');
                $length = $data['length'];
                if ($attributes->symlink) {
                    $link = $data['symlink path'] ?? throw new RuntimeException('Invalid symlink: missing link path');
                    if ($length !== 0) {
                        throw new RuntimeException('Invalid symlink: must be 0 length');
                    }
                } else {
                    $link = null; // ignore even if set
                }

                $parsedFiles[$key] = new File(
                    name: $key,
                    path: array_merge($this->path, [$key]),
                    length: $length,
                    attributes: $attributes,
                    piecesRootBin: $data['pieces root'],
                    symlinkPath: $link,
                );
                continue;
            }

            // dir
            // directories have no known params so far so just create an object
            $parsedFiles[$key] = new FileTree($file, array_merge($this->path, [$key]));
        }

        $this->files = $parsedFiles;
        $this->iterator = new \ArrayIterator($parsedFiles);
    }

    // RecursiveIterator
    public function current(): File|FileTree
    {
        return $this->iterator->current();
    }

    public function next(): void
    {
        $this->iterator->next();
    }

    public function key(): string
    {
        return \strval($this->iterator->key()); // counter array keys type cast
    }

    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    public function rewind(): void
    {
        $this->iterator->rewind();
    }

    public function hasChildren(): bool
    {
        return $this->current() instanceof FileTree;
    }

    public function getChildren(): ?FileTree
    {
        $current = $this->current();
        return $current instanceof FileTree ? $current : null;
    }

    // Countable

    public function count(): int
    {
        return \count($this->files);
    }
}
