<?php

declare(strict_types=1);

namespace Arokettu\Torrent\DataTypes\Internal;

use Arokettu\Torrent\Exception\BadMethodCallException;

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

    public function empty(): bool
    {
        return $this->data === [];
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
