<?php

declare(strict_types=1);

namespace Arokettu\Torrent\DataTypes;

use Arokettu\Bencode\Bencode;
use Arokettu\Bencode\Types\ListType;
use Arokettu\Torrent\Exception\BadMethodCallException;
use Arokettu\Torrent\Exception\OutOfBoundsException;

use function iter\chain;
use function iter\filter;

/**
 * @implements Internal\StorageInterface<int, Node>
 */
final class NodeList implements Internal\StorageInterface
{
    private array $nodes;

    /**
     * @param iterable<Node|array> $nodes
     */
    public function __construct(iterable $nodes = [])
    {
        $setOfNodes = [];

        foreach ($nodes as $node) {
            $node = Node::ensure($node);
            $setOfNodes[$this->nodeKey($node)] ??= $node;
        }

        $this->nodes = array_values($setOfNodes);
    }

    public static function create(array|Node ...$nodes): self
    {
        return new self($nodes);
    }

    public static function fromIterable(iterable $iterable): self
    {
        return new self($iterable);
    }

    public static function append(self $nodeList, array|Node ...$nodes): self
    {
        return new self(chain($nodeList, $nodes));
    }

    public static function prepend(self $nodeList, array|Node ...$nodes): self
    {
        return new self(chain($nodes, $nodeList));
    }

    public static function remove(self $nodeList, array|Node ...$nodes): self
    {
        $nodes = array_map(Node::ensure(...), $nodes);

        return new self(filter(fn ($node) => !\in_array($node, $nodes), $nodeList));
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
        return array_map(fn ($node) => $node->toArray(), $this->nodes);
    }

    /**
     * @return array<Node>
     */
    public function toArrayOfNodes(): array
    {
        return $this->nodes;
    }

    // IteratorAggregate

    public function getIterator(): \Generator
    {
        yield from $this->nodes;
    }

    // BencodeSerializable

    public function bencodeSerialize(): ?ListType
    {
        // return null for empty list
        return $this->nodes === [] ? null : new ListType($this);
    }

    // Countable

    public function count(): int
    {
        return \count($this->nodes);
    }

    // ArrayAccess

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->nodes[$offset]);
    }

    public function offsetGet(mixed $offset): Node
    {
        if (isset($this->nodes[$offset])) {
            return $this->nodes[$offset];
        }

        throw new OutOfBoundsException('Unknown offset');
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new BadMethodCallException('NodeList is immutable');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new BadMethodCallException('NodeList is immutable');
    }
}
