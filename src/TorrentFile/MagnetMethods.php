<?php

declare(strict_types=1);

namespace SandFox\Torrent\TorrentFile;

use League\Uri\QueryString;
use SandFox\Torrent\DataTypes\AnnounceList;

/**
 * @internal
 */
trait MagnetMethods
{
    abstract public function getInfoHash(): string;
    abstract public function getAnnounce(): ?string;
    abstract public function getAnnounceList(): AnnounceList;
    abstract public function getDisplayName(): ?string;

    public function getMagnetLink(): string
    {
        $pairs = [['xt', 'urn:btih:' . $this->getInfoHash()]];

        $dn = $this->getName() ?? '';
        if ($dn !== '') {
            $pairs[] = ['dn', $dn];
        }

        $trackers = [];

        $rootTracker = $this->getAnnounce();

        if ($rootTracker) {
            $trackers[] = $rootTracker;
        }

        foreach ($this->getAnnounceList() as $trGroup) {
            foreach ($trGroup as $tracker) {
                $trackers[] = $tracker;
            }
        }

        foreach (array_unique($trackers) as $tr) {
            $pairs[] = ['tr', $tr];
        }

        $query = QueryString::build($pairs);

        return 'magnet:?' . $query;
    }
}
