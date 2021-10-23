<?php

declare(strict_types=1);

namespace SandFox\Torrent\TorrentFile\Fields;

use SandFox\Torrent\DataTypes\AnnounceList as AnnounceListType;

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
        return $this->announceList ??= new AnnounceListType($this->data['announce-list'] ?? []);
    }

    /**
     * @deprecated use getAnnounceList()->toArray()
     * @return array<array<string>>
     */
    public function getAnnounceListAsArray(): array
    {
        trigger_deprecation(
            'sandfoxme/torrent-file',
            '3.0.0',
            'Use getAnnounceList()->toArray() instead of getAnnounceListAsArray()',
        );
        return $this->getAnnounceList()->toArray();
    }

    /**
     * @deprecated use getAnnounceList()
     */
    public function getAnnounceListAsObject(): AnnounceListType
    {
        trigger_deprecation(
            'sandfoxme/torrent-file',
            '3.0.0',
            'Use getAnnounceList() instead of getAnnounceListAsObject()',
        );
        return $this->getAnnounceList();
    }

    /**
     * @param AnnounceListType|iterable<string|iterable<string>>|null $announceList
     */
    public function setAnnounceList(AnnounceListType|iterable|null $announceList): self
    {
        $this->setField(
            'announce-list',
            $this->announceList = $announceList instanceof AnnounceListType ?
                $announceList :
                AnnounceListType::fromIterable($announceList ?? [])
        );

        return $this;
    }
}
