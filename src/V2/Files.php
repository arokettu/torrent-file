<?php

declare(strict_types=1);

namespace Arokettu\Torrent\V2;

use Arokettu\Torrent\Common\Attributes;
use Arokettu\Torrent\DataTypes\Internal\DictObject;
use Arokettu\Torrent\Exception\RuntimeException;

/**
 * @implements \RecursiveIterator<string, File|Files>
 */
final class Files implements \RecursiveIterator, \Countable
{
    /** @var array<string, File|Files> */
    public readonly array $files;
    /** @var \ArrayIterator<string, File|Files> */
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

                $parsedFiles[] = new File(
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
            $parsedFiles[] = new Files($file, array_merge($this->path, [$key]));
        }

        $this->files = $parsedFiles;
        $this->iterator = new \ArrayIterator($parsedFiles);
    }

    // RecursiveIterator
    public function current(): File|Files
    {
        return $this->iterator->current();
    }

    public function next(): void
    {
        $this->iterator->next();
    }

    public function key(): string
    {
        return $this->iterator->key();
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
        return $this->current() instanceof Files;
    }

    public function getChildren(): ?Files
    {
        $current = $this->current();
        return $current instanceof Files ? $current : null;
    }

    // Countable

    public function count(): int
    {
        return \count($this->files);
    }
}
