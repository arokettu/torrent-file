<?php

declare(strict_types=1);

namespace Arokettu\Torrent\DataTypes;

use Arokettu\Bencode\Bencode;
use Arokettu\Bencode\Types\ListType;
use Arokettu\Torrent\Exception\BadMethodCallException;
use Arokettu\Torrent\Exception\OutOfBoundsException;
use SandFox\Torrent\DataTypes\Internal\ListObject;

use function iter\chain;
use function iter\filter;
use function iter\map;

/**
 * @implements Internal\StorageInterface<int, UriList>
 */
final class AnnounceList implements Internal\StorageInterface
{
    use Internal\ImmutableStorage;

    public function __construct(UriList ...$uriLists)
    {
        $setOfUriLists = [];

        // deduplication
        foreach ($uriLists as $uriList) {
            $setOfUriLists[$this->uriListKey($uriList)] ??= $uriList;
        }

        // unset empty
        unset($setOfUriLists['0:']);

        // enforce list
        $this->data = array_values($setOfUriLists);
    }

    /**
     * @internal
     */
    public static function fromInternal(?ListObject $uriLists): self
    {
        return new self(...map(UriList::fromInternal(...), $uriLists ?? []));
    }

    /**
     * @param string|iterable<string>|UriList ...$uriLists
     */
    public static function create(iterable|UriList|string ...$uriLists): self
    {
        return new self(...map(UriList::fromIterableOrString(...), $uriLists));
    }

    /**
     * @param iterable<string|iterable<string>|UriList> $iterable
     */
    public static function fromIterable(iterable $iterable): self
    {
        if ($iterable instanceof self) {
            return $iterable;
        }
        return self::create(...$iterable);
    }

    /**
     * @param iterable<string>|UriList ...$uriLists
     */
    public static function append(self $announceList, iterable|UriList ...$uriLists): self
    {
        return self::fromIterable(chain($announceList, $uriLists));
    }

    /**
     * @param iterable<string>|UriList ...$uriLists
     */
    public static function prepend(self $announceList, iterable|UriList ...$uriLists): self
    {
        return self::fromIterable(chain($uriLists, $announceList));
    }

    /**
     * @param iterable<string>|UriList|string ...$uriLists
     */
    public static function remove(self $announceList, iterable|UriList|string ...$uriLists): self
    {
        $uriLists = array_map(UriList::fromIterableOrString(...), $uriLists);

        return self::fromIterable(filter(fn ($uriList) => !\in_array($uriList, $uriLists), $announceList));
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
        return array_map(fn ($uriList) => $uriList->toArray(), $this->data);
    }

    /**
     * @return array<UriList>
     */
    public function toArrayOfUriLists(): array
    {
        return $this->data;
    }

    // IteratorAggregate

    public function getIterator(): \Generator
    {
        yield from $this->data;
    }

    // BencodeSerializable

    public function bencodeSerialize(): ?ListType
    {
        // return null for empty list
        return $this->data === [] ? null : new ListType($this);
    }

    // ArrayAccess

    public function offsetGet(mixed $offset): UriList
    {
        if (isset($this->data[$offset])) {
            return $this->data[$offset];
        }

        throw new OutOfBoundsException('Unknown offset');
    }
}
