<?php

declare(strict_types=1);

namespace SandFox\Torrent\TorrentFile\Fields;

use SandFox\Torrent\DataTypes\UriList;

trait HttpSeeds
{
    private ?UriList $httpseeds = null;

    public function getHttpSeeds(): UriList
    {
        return $this->httpseeds ??= new UriList($this->getField('httpseeds', []));
    }

    /**
     * @param UriList|iterable<string>|null $value
     */
    public function setHttpSeeds($value): self
    {
        $this->setField(
            'httpseeds',
            $this->httpseeds = $value instanceof UriList ? $value : UriList::fromIterable($value ?? [])
        );
        return $this;
    }
}
