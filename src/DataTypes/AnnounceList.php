<?php

declare(strict_types=1);

namespace SandFox\Torrent\DataTypes;

use SandFox\Bencode\Bencode;
use SandFox\Bencode\Types\BencodeSerializable;
use SandFox\Bencode\Types\ListType;
use SandFox\Torrent\Exception\BadMethodCallException;
use SandFox\Torrent\Exception\OutOfBoundsException;

use function iter\chain;
use function iter\filter;
use function iter\map;

final class AnnounceList implements \IteratorAggregate, BencodeSerializable, \Countable, \ArrayAccess
{
    private array $uriLists;

    /**
     * @param iterable<UriList|iterable<string>> $uriLists
     */
    public function __construct(iterable $uriLists = [])
    {
        $setOfUriLists = [];

        foreach ($uriLists as $uriList) {
            $uriList = UriList::ensure($uriList);
            $setOfUriLists[$this->uriListKey($uriList)] ??= $uriList;
        }

        // unset empty
        unset($setOfUriLists['0:']);

        $this->uriLists = array_values($setOfUriLists);
    }

    /**
     * @param iterable<string>|UriList ...$uriLists
     */
    public static function create(...$uriLists): self
    {
        return new self($uriLists);
    }

    /**
     * @param iterable<string|iterable<string>> $iterable
     */
    public static function fromIterable(iterable $iterable): self
    {
        return new self(map(fn ($uriList) => is_iterable($uriList) ? $uriList : [$uriList], $iterable));
    }

    /**
     * @param iterable<string>|UriList ...$uriLists
     */
    public static function append(self $announceList, ...$uriLists): self
    {
        return new self(chain($announceList, $uriLists));
    }

    /**
     * @param iterable<string>|UriList ...$uriLists
     */
    public static function prepend(self $announceList, ...$uriLists): self
    {
        return new self(chain($uriLists, $announceList));
    }

    /**
     * @param iterable<string>|UriList ...$uriLists
     */
    public static function remove(self $announceList, ...$uriLists): self
    {
        $uriLists = array_map(fn ($uriList) => UriList::ensure($uriList), $uriLists);

        return new self(filter(fn ($uriList) => !\in_array($uriList, $uriLists), $announceList));
    }

    private function uriListKey(UriList $uriList): string
    {
        return $uriList->count() === 0 ? '0:' : Bencode::encode($uriList);
    }

    /**
     * @return array<array<string>>
     */
    public function toArray(): array
    {
        return array_map(fn ($uriList) => $uriList->toArray(), $this->uriLists);
    }

    /**
     * @return array<UriList>
     */
    public function toArrayOfUriLists(): array
    {
        return $this->uriLists;
    }

    // IteratorAggregate

    public function getIterator(): \Generator
    {
        yield from $this->uriLists;
    }

    // BencodeSerializable

    public function bencodeSerialize(): ?ListType
    {
        // return null for empty list
        return $this->uriLists === [] ? null : new ListType($this);
    }

    // Countable

    public function count(): int
    {
        return \count($this->uriLists);
    }

    // ArrayAccess

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->uriLists[$offset]);
    }

    /**
     * @param mixed $offset
     */
    public function offsetGet($offset): UriList
    {
        if (isset($this->uriLists[$offset])) {
            return $this->uriLists[$offset];
        }

        throw new OutOfBoundsException('Unknown offset');
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException('AnnounceList is immutable');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        throw new BadMethodCallException('AnnounceList is immutable');
    }
}
