<?php

declare(strict_types=1);

namespace SandFox\Torrent\DataTypes\Internal;

use SandFox\Torrent\Exception\BadMethodCallException;

/**
 * @internal
 */
trait ImmutableStorage
{
    private readonly array $data;

    // Countable

    public function count(): int
    {
        return \count($this->data);
    }

    // ArrayAccess

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->data[$offset]);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new BadMethodCallException(self::class . ' is immutable');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new BadMethodCallException(self::class . ' is immutable');
    }
}
