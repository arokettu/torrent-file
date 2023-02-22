<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile;

use Arokettu\Torrent\DataTypes\AnnounceList;
use Arokettu\Torrent\Exception\RuntimeException;
use Arokettu\Torrent\MetaVersion;
use League\Uri\QueryString;

/**
 * @internal
 */
trait MagnetMethods
{
    abstract public function getInfoHash(MetaVersion $version = null, bool $binary = false): ?string;
    abstract public function getName(): ?string;
    abstract public function getAnnounce(): ?string;
    abstract public function getAnnounceList(): AnnounceList;

    public function getMagnetLink(): string
    {
        $pairs = [];

        $hash = false;
        if ($this->getInfoHash(MetaVersion::V1)) {
            $hash = true;
            $pairs[] = ['xt', 'urn:btih:' . $this->getInfoHash(MetaVersion::V1)];
        }
        if ($this->getInfoHash(MetaVersion::V2)) {
            $hash = true;
            $pairs[] = ['xt', 'urn:btmh:' . bin2hex("\x12\x20") . $this->getInfoHash(MetaVersion::V2)];
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
