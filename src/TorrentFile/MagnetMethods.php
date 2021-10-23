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
    abstract private function getInfoHash(): string;
    abstract private function getName(): ?string;
    abstract private function getAnnounce(): ?string;
    abstract private function getAnnounceList(): AnnounceList;

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
