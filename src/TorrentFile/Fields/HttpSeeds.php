<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile\Fields;

use Arokettu\Torrent\DataTypes\UriList;

trait HttpSeeds
{
    private ?UriList $httpseeds = null;

    abstract private function getField(string $key, mixed $default = null): mixed;
    abstract private function setField(string $key, mixed $value): void;

    public function getHttpSeeds(): UriList
    {
        return $this->httpseeds ??= new UriList($this->getField('httpseeds', []));
    }

    /**
     * @param UriList|iterable<string>|null $value
     */
    public function setHttpSeeds(UriList|iterable|null $value): self
    {
        $this->setField(
            'httpseeds',
            $this->httpseeds = $value instanceof UriList ? $value : UriList::fromIterable($value ?? [])
        );
        return $this;
    }
}
