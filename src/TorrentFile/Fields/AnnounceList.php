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

    /**
     * @deprecated Alias of getAnnounceListAsArray(). In 3.0 it will be an alias of getAnnounceListAsObject()
     * @return array<array<string>>
     */
    public function getAnnounceList(): array
    {
        trigger_deprecation(
            'sandfoxme/bencode',
            '2.2.0',
            'getAnnounceList() will return an instance of AnnounceList in 3.0. ' .
            'Use getAnnounceListAsArray() for future compatibility.'
        );
        return $this->getAnnounceListAsObject()->toArray();
    }

    /**
     * @return array<array<string>>
     */
    public function getAnnounceListAsArray(): array
    {
        return $this->getAnnounceListAsObject()->toArray();
    }

    public function getAnnounceListAsObject(): AnnounceListType
    {
        return $this->announceList ??= new AnnounceListType($this->getField('announce-list', []));
    }

    /**
     * @param AnnounceListType|iterable<string|iterable<string>>|null $announceList
     */
    public function setAnnounceList($announceList): self
    {
        $this->setField(
            'announce-list',
            $this->announceList = $announceList instanceof AnnounceListType ?
                $announceList :
                AnnounceListType::fromArray($announceList ?? [])
        );

        return $this;
    }
}
