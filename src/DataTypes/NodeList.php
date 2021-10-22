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

final class NodeList implements \IteratorAggregate, BencodeSerializable, \Countable, \ArrayAccess
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

    /**
     * @param array|Node ...$nodes
     */
    public static function create(...$nodes): self
    {
        return new self($nodes);
    }

    public static function fromIterable(iterable $iterable): self
    {
        return new self($iterable);
    }

    /**
     * @param array|Node ...$nodes
     */
    public static function append(self $nodeList, ...$nodes): self
    {
        return new self(chain($nodeList, $nodes));
    }

    /**
     * @param array|Node ...$nodes
     */
    public static function prepend(self $nodeList, ...$nodes): self
    {
        return new self(chain($nodes, $nodeList));
    }

    /**
     * @param array|Node ...$nodes
     */
    public static function remove(self $nodeList, ...$nodes): self
    {
        $nodes = array_map(fn ($node) => Node::ensure($node), $nodes);

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
