<?php

declare(strict_types=1);

namespace SandFox\Torrent\DataTypes;

use JetBrains\PhpStorm\ArrayShape;
use SandFox\Bencode\Types\BencodeSerializable;
use SandFox\Torrent\Exception\BadMethodCallException;
use SandFox\Torrent\Exception\InvalidArgumentException;
use SandFox\Torrent\Exception\OutOfBoundsException;

final class Node implements BencodeSerializable, \ArrayAccess
{
    private string $host;
    private int $port;

    public function __construct(string $host, int $port)
    {
        $this->host = $host;
        $this->port = $port;
    }

    public static function fromArray(array $array): self
    {
        if (\count($array) !== 2 || !array_is_list($array)) {
            throw new InvalidArgumentException('$array must contain 2 values and be a list');
        }

        return new self($array[0], $array[1]);
    }

    public static function ensure(array|self $node): self
    {
        return $node instanceof self ? $node : self::fromArray($node);
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
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
            0 => $this->getHost(),
            1 => $this->getPort(),
            default => throw new OutOfBoundsException('Unknown offset'),
        };
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new BadMethodCallException('Node is immutable');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new BadMethodCallException('Node is immutable');
    }
}
