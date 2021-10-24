<?php

declare(strict_types=1);

namespace SandFox\Torrent\TorrentFile\Fields;

use SandFox\Torrent\DataTypes\Node;
use SandFox\Torrent\DataTypes\NodeList;

trait Nodes
{
    private ?NodeList $nodes = null;

    public function getNodes(): NodeList
    {
        return $this->nodes ??= new NodeList($this->getField('nodes', []));
    }

    /**
     * @param NodeList|iterable<Node|array>|null $value
     */
    public function setNodes($value): self
    {
        $this->setField(
            'nodes',
            $this->nodes = $value instanceof NodeList ? $value : NodeList::fromIterable($value ?? [])
        );
        return $this;
    }
}
