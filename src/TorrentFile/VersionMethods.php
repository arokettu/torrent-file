<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile;

use Arokettu\Torrent\DataTypes\Internal\InfoDict;
use Arokettu\Torrent\DataTypes\Internal\Undefined;
use Arokettu\Torrent\MetaVersion;
use Arokettu\Torrent\TorrentFile\V1\Info as InfoV1;
use Arokettu\Torrent\TorrentFile\V2\Info as InfoV2;

trait VersionMethods
{
    private InfoV1|Undefined|null $v1 = Undefined::Undefined;
    private InfoV2|Undefined|null $v2 = Undefined::Undefined;

    abstract public function hasMetadata(MetaVersion $version): bool;
    abstract private function info(): InfoDict;

    public function v1(): ?InfoV1
    {
        if ($this->v1 === Undefined::Undefined) {
            $this->v1 = $this->hasMetadata(MetaVersion::V1) ? new InfoV1($this->info()) : null;
        }

        return $this->v1;
    }

    public function v2(): ?InfoV2
    {
        if ($this->v2 === Undefined::Undefined) {
            $this->v2 = $this->hasMetadata(MetaVersion::V2) ? new InfoV2($this->info()) : null;
        }

        return $this->v2;
    }

    private function resetCachedVersionObjects(): void
    {
        $this->v1 = Undefined::Undefined;
        $this->v2 = Undefined::Undefined;
    }
}
