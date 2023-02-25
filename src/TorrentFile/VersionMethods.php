<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile;

use Arokettu\Torrent\DataTypes\Internal\InfoDict;
use Arokettu\Torrent\DataTypes\Internal\Undefined;
use Arokettu\Torrent\V1\Info as InfoV1;
use Arokettu\Torrent\V2\Info as InfoV2;

trait VersionMethods
{
    private InfoV1|Undefined|null $v1 = Undefined::Undefined;
    private InfoV2|Undefined|null $v2 = Undefined::Undefined;

    abstract private function info(): InfoDict;

    public function v1(): ?InfoV1
    {
        return $this->v1 === Undefined::Undefined ? $this->v1 = new InfoV1($this->info()) : $this->v1;
    }

    public function v2(): ?InfoV2
    {
        return $this->v2 === Undefined::Undefined ? $this->v2 = new InfoV2($this->info()) : $this->v2;
    }
}
