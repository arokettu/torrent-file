<?php

declare(strict_types=1);

namespace Arokettu\Torrent\DataTypes;

use Arokettu\Bencode\Types\ListType;
use Arokettu\Torrent\Exception\BadMethodCallException;
use Arokettu\Torrent\Exception\InvalidArgumentException;
use Arokettu\Torrent\Exception\OutOfBoundsException;
use SandFox\Torrent\DataTypes\Internal\ListObject;

use function iter\chain;
use function iter\filter;

/**
 * @implements Internal\StorageInterface<int, string>
 */
final class UriList implements Internal\StorageInterface
{
    use Internal\ImmutableStorage;

    public function __construct(string ...$uris)
    {
        $setOfUris = [];

        // deduplicate
        foreach ($uris as $uri) {
            $setOfUris[$uri] ??= $uri;
        }

        // enforce list
        $this->data = array_values($setOfUris);
    }

    /**
     * @internal
     */
    public static function fromInternal(?ListObject $uris): self
    {
        return self::fromIterable($uris ?? []);
    }

    /**
     * @internal
     *
     * BEP-0019 allows url-list to be a string not list
     */
    public static function fromInternalUrlList(ListObject|string|null $uris): self
    {
        if (\is_string($uris)) {
            return new self($uris);
        }

        return self::fromInternal($uris);
    }

    public static function create(string ...$uris): self
    {
        return new self(...$uris);
    }

    public static function fromIterable(iterable $iterable): self
    {
        if ($iterable instanceof self) {
            return $iterable;
        }
        return new self(...$iterable);
    }

    public static function append(self $uriList, string ...$uris): self
    {
        return self::fromIterable(chain($uriList, $uris));
    }

    public static function prepend(self $uriList, string ...$uris): self
    {
        return self::fromIterable(chain($uris, $uriList));
    }

    public static function remove(self $uriList, string ...$uris): self
    {
        return self::fromIterable(filter(fn ($uri) => !\in_array($uri, $uris), $uriList));
    }

    /**
     * @return array<string>
     */
    public function toArray(): array
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

    public function offsetGet(mixed $offset): string
    {
        if (isset($this->data[$offset])) {
            return $this->data[$offset];
        }

        throw new OutOfBoundsException('Unknown offset');
    }
}
