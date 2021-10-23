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

    public static function fromIterable(iterable $iterable): self
    {
        return new self($iterable);
    }

    /**
     * @internal
     * @param iterable<string>|self $uriList
     */
    public static function ensure(iterable|self $uriList): self
    {
        return $uriList instanceof self ? $uriList : new self($uriList);
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

    /**
     * @return array<string>
     */
    public function toArray(): array
    {
        return $this->uris;
    }

    // IteratorAggregate

    public function getIterator(): \Generator
    {
        yield from $this->uris;
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

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->uris[$offset]);
    }

    public function offsetGet(mixed $offset): string
    {
        if (isset($this->uris[$offset])) {
            return $this->uris[$offset];
        }

        throw new OutOfBoundsException('Unknown offset');
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new BadMethodCallException('UriList is immutable');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new BadMethodCallException('UriList is immutable');
    }
}
