<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile;

use Arokettu\Torrent\DataTypes\AnnounceList;
use Arokettu\Torrent\Exception\RuntimeException;
use Arokettu\Torrent\TorrentFile\V1\Info as InfoV1;
use Arokettu\Torrent\TorrentFile\V2\Info as InfoV2;
use League\Uri\QueryString;

/**
 * @internal
 */
trait MagnetMethods
{
    abstract public function v1(): ?InfoV1;
    abstract public function v2(): ?InfoV2;
    abstract public function getName(): ?string;
    abstract public function getAnnounce(): ?string;
    abstract public function getAnnounceList(): AnnounceList;

    public function getMagnetLink(): string
    {
        $pairs = [];

        $hash = false;
        if ($this->v1()) {
            $hash = true;
            $pairs[] = ['xt', 'urn:btih:' . $this->v1()->getInfoHash()];
        }
        if ($this->v2()) {
            $hash = true;
            $pairs[] = ['xt', 'urn:btmh:' . bin2hex("\x12\x20") . $this->v2()->getInfoHash()];
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
