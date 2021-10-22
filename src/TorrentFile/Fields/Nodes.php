<?php

declare(strict_types=1);

namespace SandFox\Torrent\TorrentFile\Fields;

use SandFox\Torrent\DataTypes\NodeList;

trait Nodes
{
    private ?NodeList $nodes = null;

    public function getNodes(): NodeList
    {
        return $this->nodes ??= $this->buildNodeListFromExternalValue($this->data['nodes'] ?? []);
    }

    /**
     * @param NodeList|iterable|null $value
     */
    public function setNodes($value): self
    {
        $this->data['nodes'] = $this->buildNodeListFromExternalValue($value);

        return $this;
    }

    /**
     * @param NodeList|iterable|null $value
     */
    private function buildNodeListFromExternalValue($value): NodeList
    {
        if ($value instanceof NodeList) {
            return $value;
        }

        return new NodeList($value ?? []);
    }
}
