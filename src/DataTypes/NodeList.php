<?php

declare(strict_types=1);

namespace Arokettu\Torrent\DataTypes;

use Arokettu\Bencode\Bencode;
use Arokettu\Bencode\Types\ListType;
use Arokettu\Torrent\DataTypes\Internal\ListObject;
use Arokettu\Torrent\Exception\OutOfBoundsException;

use function iter\chain;
use function iter\filter;
use function iter\map;

/**
 * @implements Internal\StorageInterface<int, Node>
 */
final class NodeList implements Internal\StorageInterface
{
    use Internal\ImmutableStorage;

    public function __construct(Node ...$nodes)
    {
        $setOfNodes = [];

        // deduplication
        foreach ($nodes as $node) {
            $setOfNodes[$this->nodeKey($node)] ??= $node;
        }

        // enforce list
        $this->data = array_values($setOfNodes);
    }

    public static function create(array|Node ...$nodes): self
    {
        return new self(...map(fn ($node) => \is_array($node) ? Node::fromArray($node) : $node, $nodes));
    }

    /**
     * @internal
     */
    public static function fromInternal(?ListObject $nodes): self
    {
        return new self(...map(Node::fromInternal(...), $nodes ?? []));
    }

    public static function fromIterable(iterable $iterable): self
    {
        if ($iterable instanceof self) {
            return $iterable;
        }
        return self::create(...$iterable);
    }

    public static function append(self $nodeList, array|Node ...$nodes): self
    {
        return self::fromIterable(chain($nodeList, $nodes));
    }

    public static function prepend(self $nodeList, array|Node ...$nodes): self
    {
        return self::fromIterable(chain($nodes, $nodeList));
    }

    public static function remove(self $nodeList, array|Node ...$nodes): self
    {
        $nodes = array_map(fn ($node) => \is_array($node) ? Node::fromArray($node) : $node, $nodes);
        return self::fromIterable(filter(fn ($node) => !\in_array($node, $nodes), $nodeList));
    }

    private function nodeKey(Node $node): string
    {
        return Bencode::encode($node);
    }

    /**
     * @return array<array<int|string>>
     */
    public function toArray(): array
    {
        return array_map(fn ($node) => $node->toArray(), $this->data);
    }

    /**
     * @return array<Node>
     */
    public function toArrayOfNodes(): array
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

    public function offsetGet(mixed $offset): Node
    {
        if (isset($this->data[$offset])) {
            return $this->data[$offset];
        }

        throw new OutOfBoundsException('Unknown offset');
    }
}
