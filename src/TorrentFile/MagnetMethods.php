<?php

declare(strict_types=1);

namespace SandFox\Torrent\TorrentFile;

use League\Uri\QueryString;

/**
 * @internal
 */
trait MagnetMethods
{
    public function getMagnetLink(): string
    {
        $pairs = [['xt', 'urn:btih:' . strtoupper($this->getInfoHash())]];

        $dn = $this->data['info']['name'] ?? '';
        if ($dn !== '') {
            $pairs[] = ['dn', $this->getDisplayName()];
        }

        $trackers = [];

        $rootTracker = $this->getAnnounce();

        if ($rootTracker) {
            $trackers[] = $rootTracker;
        }

        foreach ($this->getAnnounceListAsObject() as $trGroup) {
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
