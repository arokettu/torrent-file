<?php

declare(strict_types=1);

namespace SandFox\Torrent\TorrentFile;

use League\Uri\QueryString;
use SandFox\Torrent\DataTypes\AnnounceList;
use SandFox\Torrent\Exception\RuntimeException;

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
        $pairs = [];

        $hash = false;
        if ($this->getInfoHashV1()) {
            $hash = true;
            $pairs[] = ['xt', 'urn:btih:' . $this->getInfoHashV1()];
        }
        if ($this->getInfoHashV2()) {
            $hash = true;
            $pairs[] = ['xt', 'urn:btmh:' . bin2hex("\x12\x20") . $this->getInfoHashV2()];
        }

        if ($hash === false) {
            throw new RuntimeException('Trying to create a magnet link for a file without valid metadata');
        }

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
