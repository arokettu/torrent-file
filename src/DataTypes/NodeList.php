<?php

declare(strict_types=1);

namespace SandFox\Torrent\DataTypes;

use SandFox\Bencode\Bencode;
use SandFox\Bencode\Types\BencodeSerializable;
use SandFox\Bencode\Types\ListType;
use SandFox\Torrent\Exception\BadMethodCallException;
use SandFox\Torrent\Exception\OutOfBoundsException;

final class NodeList implements \IteratorAggregate, BencodeSerializable, \Countable, \ArrayAccess
{
    private array $nodes;

    public function __construct(iterable $nodes = [])
    {
        $setOfNodes = [];

        foreach ($nodes as $node) {
            $node = $this->ensureNode($node);
            $setOfNodes[$this->nodeKey($node)] ??= $node;
        }

        $this->nodes = array_values($setOfNodes);
    }

    /**
     * @param array|self $node
     */
    private function ensureNode($node): Node
    {
        if ($node instanceof Node) {
            return $node;
        }

        if (\is_array($node)) {
            return Node::fromArray($node);
        }

        throw new \InvalidArgumentException('$node must be an instance of Node or array[2]');
    }

    private function nodeKey(Node $node): string
    {
        return Bencode::encode($node);
    }

    public function toArray(): array
    {
        return array_map(fn ($node) => $node->toArray(), $this->nodes);
    }

    public function toArrayOfNodes(): array
    {
        return $this->nodes;
    }

    // IteratorAggregate

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->nodes);
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

    /**
     * @param mixed $offset
     */
    public function offsetExists($offset): bool
    {
        return isset($this->nodes[$offset]);
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        if (isset($this->nodes[$offset])) {
            return $this->nodes[$offset];
        }

        throw new OutOfBoundsException('Unknown offset');
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        throw new BadMethodCallException('NodeList is immutable');
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        throw new BadMethodCallException('NodeList is immutable');
    }
}
