<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile\Fields;

use Arokettu\Torrent\DataTypes\Node;
use Arokettu\Torrent\DataTypes\NodeList;

/**
 * @internal
 */
trait Nodes
{
    private ?NodeList $nodes = null;

    abstract private function getField(string $key, mixed $default = null): mixed;
    abstract private function setField(string $key, mixed $value): void;

    public function getNodes(): NodeList
    {
        return $this->nodes ??= NodeList::fromInternal($this->getField('nodes'));
    }

    /**
     * @param NodeList|iterable<Node|array>|null $value
     */
    public function setNodes(NodeList|iterable|null $value): self
    {
        $this->nodes = NodeList::fromIterable($value ?? []);
        $this->setField('nodes', $this->nodes);
        return $this;
    }
}
