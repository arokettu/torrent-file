<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile;

use Arokettu\Torrent\Exception\RuntimeException;
use Arokettu\Torrent\TorrentFile\V1\Info as InfoV1;
use Arokettu\Torrent\TorrentFile\V2\Info as InfoV2;

/**
 * @internal
 */
trait NameMethods
{
    abstract public function v1(): ?InfoV1;
    abstract public function v2(): ?InfoV2;
    abstract public function getName(): ?string;

    public function getDisplayName(): string
    {
        $name = $this->getName() ?? '';
        if ($name === '') { // unset or empty
            $name = $this->v2()?->getInfoHash() ?? $this->v1()?->getInfoHash() ??
                throw new RuntimeException('Unable to generate a name: both name and hash are missing');
        }
        return $name;
    }

    public function getFileName(): string
    {
        return $this->getDisplayName() . '.torrent';
    }
}
