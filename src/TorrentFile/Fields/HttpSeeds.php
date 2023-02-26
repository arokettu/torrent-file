<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile\Fields;

use Arokettu\Torrent\DataTypes\UriList;

/**
 * @internal
 */
trait HttpSeeds
{
    private ?UriList $httpseeds = null;

    abstract private function getField(string $key): mixed;
    abstract private function setField(string $key, mixed $value): void;

    public function getHttpSeeds(): UriList
    {
        return $this->httpseeds ??= UriList::fromInternal($this->getField('httpseeds'));
    }

    /**
     * @param UriList|iterable<string>|null $value
     */
    public function setHttpSeeds(UriList|iterable|null $value): self
    {
        $this->httpseeds = UriList::fromIterable($value ?? []);
        $this->setField('httpseeds', $this->httpseeds);
        return $this;
    }
}
