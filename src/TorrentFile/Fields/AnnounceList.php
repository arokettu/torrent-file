<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile\Fields;

use Arokettu\Torrent\DataTypes\AnnounceList as AnnounceListType;

/**
 * @internal
 */
trait AnnounceList
{
    private ?AnnounceListType $announceList = null;

    abstract private function getField(string $key, mixed $default = null): mixed;
    abstract private function setField(string $key, mixed $value): void;

    public function getAnnounceList(): AnnounceListType
    {
        return $this->announceList ??= AnnounceListType::fromInternal($this->getField('announce-list'));
    }

    /**
     * @param AnnounceListType|iterable<string|iterable<string>>|null $announceList
     */
    public function setAnnounceList(AnnounceListType|iterable|null $announceList): self
    {
        $this->announceList = AnnounceListType::fromIterable($announceList ?? []);
        $this->setField('announce-list', $this->announceList);
        return $this;
    }
}
