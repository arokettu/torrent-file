<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile\Common;

use Arokettu\Torrent\Exception\InvalidArgumentException;

/**
 * @property-read bool $x
 * @property-read bool $executable
 *
 * @property-read bool $l
 * @property-read bool $symlink
 *
 * @property-read bool $p
 * @property-read bool $pad
 */
final class Attributes
{
    public readonly string $attr;

    public readonly bool $x;
    public readonly bool $executable;
    public readonly bool $l;
    public readonly bool $symlink;
    public readonly bool $p;
    public readonly bool $pad;

    public function __construct(string $attr)
    {
        $this->attr = $attr;

        $this->x = $this->executable = str_contains($attr, 'x');
        $this->l = $this->symlink = str_contains($attr, 'l');
        $this->p = $this->pad = str_contains($attr, 'p');
    }

    public function has(string $attr): bool
    {
        if (\strlen($attr) !== 1) {
            throw new InvalidArgumentException('Attribute name must be 1 character long');
        }
        return str_contains($this->attr, $attr);
    }

    public function __get(string $name): bool
    {
        return $this->has($name);
    }
}
