<?php

declare(strict_types=1);

namespace SandFox\Torrent\DataTypes;

use SandFox\Bencode\Types\BencodeSerializable;
use SandFox\Bencode\Types\ListType;
use SandFox\Torrent\Exception\BadMethodCallException;
use SandFox\Torrent\Exception\InvalidArgumentException;
use SandFox\Torrent\Exception\OutOfBoundsException;

use function iter\chain;
use function iter\filter;

final class UriList implements \IteratorAggregate, BencodeSerializable, \Countable, \ArrayAccess
{
    private array $uris;

    /**
     * @param iterable<string> $uris
     */
    public function __construct(iterable $uris = [])
    {
        $setOfUris = [];

        foreach ($uris as $uri) {
            if (!\is_string($uri)) {
                throw new InvalidArgumentException('Uri instances must be strings');
            }
            $setOfUris[$uri] ??= $uri;
        }

        $this->uris = array_values($setOfUris);
    }

    public static function create(string ...$uris): self
    {
        return new self($uris);
    }

    /**
     * @internal
     * @param iterable<string>|self $uriList
     * @return static
     */
    public static function ensure($uriList): self
    {
        if ($uriList instanceof self) {
            return $uriList;
        }

        if (\is_iterable($uriList)) {
            return new self($uriList);
        }

        throw new InvalidArgumentException('$uriList must be an instance of UriList or iterable<string>');
    }


    public static function append(self $uriList, string ...$uris): self
    {
        return new self(chain($uriList, $uris));
    }

    public static function prepend(self $uriList, string ...$uris): self
    {
        return new self(chain($uris, $uriList));
    }

    public static function remove(self $uriList, string ...$uris): self
    {
        return new self(filter(fn ($uri) => !\in_array($uri, $uris), $uriList));
    }

    public function toArray(): array
    {
        return $this->uris;
    }

    // IteratorAggregate

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->uris);
    }

    // BencodeSerializable

    public function bencodeSerialize(): ?ListType
    {
        // return null for empty list
        return $this->uris === [] ? null : new ListType($this);
    }

    // Countable

    public function count(): int
    {
        return \count($this->uris);
    }

    // ArrayAccess

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->uris[$offset]);
    }

    /**
     * @param mixed $offset
     */
    public function offsetGet($offset): string
    {
        if (isset($this->uris[$offset])) {
            return $this->uris[$offset];
        }

        throw new OutOfBoundsException('Unknown offset');
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException('UriList is immutable');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        throw new BadMethodCallException('UriList is immutable');
    }
}
