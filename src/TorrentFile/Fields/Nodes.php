<?php

declare(strict_types=1);

namespace SandFox\Torrent\TorrentFile\Fields;

use SandFox\Torrent\DataTypes\Node;
use SandFox\Torrent\DataTypes\NodeList;

trait Nodes
{
    private ?NodeList $nodes = null;

    abstract private function getField(string $key, mixed $default = null): mixed;
    abstract private function setField(string $key, mixed $value): void;

    public function getNodes(): NodeList
    {
        return $this->nodes ??= new NodeList($this->getField('nodes', []));
    }

    /**
     * @param NodeList|iterable<Node|array>|null $value
     */
    public function setNodes(NodeList|iterable|null $value): self
    {
        $this->setField(
            'nodes',
            $this->nodes = $value instanceof NodeList ? $value : NodeList::fromIterable($value ?? [])
        );
        return $this;
    }
}
