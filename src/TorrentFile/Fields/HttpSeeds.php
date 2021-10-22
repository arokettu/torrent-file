<?php

declare(strict_types=1);

namespace SandFox\Torrent\TorrentFile\Fields;

use SandFox\Torrent\DataTypes\UriList;

trait HttpSeeds
{
    private ?UriList $httpseeds = null;

    public function getHttpSeeds(): UriList
    {
        return $this->httpseeds ??= new UriList($this->data['httpseeds'] ?? []);
    }

    /**
     * @param UriList|iterable<string>|null $value
     */
    public function setHttpSeeds($value): self
    {
        $this->httpseeds = $this->data['httpseeds'] = $value instanceof UriList ? $value : new UriList($value ?? []);

        return $this;
    }
}
