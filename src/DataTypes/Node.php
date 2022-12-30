<?php

declare(strict_types=1);

namespace Arokettu\Torrent\DataTypes;

use Arokettu\Bencode\Types\BencodeSerializable;
use Arokettu\Torrent\DataTypes\Internal\ArrayInterface;
use Arokettu\Torrent\DataTypes\Internal\ListObject;
use Arokettu\Torrent\Exception\BadMethodCallException;
use Arokettu\Torrent\Exception\InvalidArgumentException;
use Arokettu\Torrent\Exception\OutOfBoundsException;
use ArrayAccess;
use JetBrains\PhpStorm\ArrayShape;

final class Node implements ArrayAccess, ArrayInterface, BencodeSerializable
{
    public function __construct(
        public readonly string $host,
        public readonly int $port,
    ) {}

    /**
     * @internal
     */
    public static function fromInternal(ListObject $node): self
    {
        return self::fromArray($node->toArray());
    }

    public static function fromArray(array $array): self
    {
        if (\count($array) !== 2 || !array_is_list($array)) {
            throw new InvalidArgumentException('$array must contain 2 values and be a list');
        }

        return new self($array[0], $array[1]);
    }

    #[ArrayShape(['string', 'int'])]
    public function toArray(): array
    {
        return [$this->host, $this->port];
    }

    // BencodeSerializable

    public function bencodeSerialize(): array
    {
        return $this->toArray();
    }

    // ArrayAccess

    public function offsetExists(mixed $offset): bool
    {
        return $offset === 0 || $offset === 1;
    }

    public function offsetGet(mixed $offset): int|string
    {
        return match ($offset) {
            0 => $this->host,
            1 => $this->port,
            default => throw new OutOfBoundsException('Unknown offset'),
        };
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
