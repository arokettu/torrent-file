<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Common;

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
    private readonly array $attributes;

    public function __construct(string $attr)
    {
        $this->attributes = array_fill_keys(str_split($attr), 1);
    }

    public function all(): array
    {
        return array_keys($this->attributes);
    }

    public function has(string $attr): bool
    {
        if (\strlen($attr) !== 1) {
            throw new InvalidArgumentException('Attribute name must be 1 character long');
        }
        return isset($this->attributes[$attr]);
    }

    public function isSymlink(): bool
    {
        return $this->has('l');
    }

    public function isPad(): bool
    {
        return $this->has('p');
    }

    public function isExecutable(): bool
    {
        return $this->has('x');
    }

    public function __get(string $name): bool
    {
        return match ($name) {
            'symlink' => $this->isSymlink(),
            'pad' => $this->isPad(),
            'executable' => $this->isExecutable(),
            default => $this->has($name),
        };
    }
}
