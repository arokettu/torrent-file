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

    /**
     * @internal
     * @param array|Node $node
     */
    public static function ensure($node): self
    {
        if ($node instanceof self) {
            return $node;
        }

        if (\is_array($node)) {
            return self::fromArray($node);
        }

        throw new InvalidArgumentException('$node must be an instance of Node or array[2]');
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

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return $offset === 0 || $offset === 1;
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        switch ($offset) {
            case 0:
                return $this->host;
            case 1:
                return $this->port;
            default:
                throw new OutOfBoundsException('Unknown offset');
        }
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException('Node is immutable');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        throw new BadMethodCallException('Node is immutable');
    }
}
