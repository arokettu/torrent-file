<?php

declare(strict_types=1);

namespace Arokettu\Torrent\V1;

use Arokettu\Torrent\Common\Attributes;
use Arokettu\Torrent\DataTypes\Internal\ListObject;
use Arokettu\Torrent\Exception\RuntimeException;

use function iter\map;

/**
 * @implements \IteratorAggregate<int, File>
 */
final class Files implements \IteratorAggregate
{
    /** @var array<File> */
    public readonly array $files;

    public function __construct(ListObject $files)
    {
        $this->parseList($files);
    }

    private function parseList(ListObject $files): void
    {
        $this->files = [...map(function ($file) {
            $attributes = new Attributes($file['attr'] ?? '');
            $length = $file['length'];
            if ($attributes->symlink) {
                $link = $file['symlink path'] ?? throw new RuntimeException('Invalid symlink: missing link path');
                if ($length === null) { // be tolerant to missing length
                    $length = 0;
                } elseif ($length !== 0) {
                    throw new RuntimeException('Invalid symlink: must be 0 length');
                }
            } else {
                if ($length === null) {
                    throw new RuntimeException('Invalid file: missing length');
                }
                $link = null; // ignore even if set
            }

            return new File(
                path: $file['path'] ?? throw new RuntimeException('File is missing path'),
                length: $length,
                attributes: $attributes,
                sha1bin: $file['sha1'],
                symlinkPath: $link,
            );
        }, $files)];
    }

    public function getIterator(
        bool $skipPadFiles = true,
    ): \Generator {
        foreach ($this->files as $index => $file) {
            if ($skipPadFiles && $file->attributes->pad) {
                continue;
            }

            yield $index => $file;
        }
    }
}
