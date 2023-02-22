<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile;

use Arokettu\Torrent\Exception\RuntimeException;
use Arokettu\Torrent\MetaVersion;

/**
 * @internal
 */
trait NameMethods
{
    abstract public function getInfoHash(MetaVersion $version, bool $binary = false): ?string;
    abstract public function getName(): ?string;

    public function getDisplayName(): string
    {
        $name = $this->getName() ?? '';
        if ($name === '') { // unset or empty
            $name = $this->getInfoHash(MetaVersion::V2) ?? $this->getInfoHash(MetaVersion::V1) ??
                throw new RuntimeException('Unable to generate a name: both name and hash are missing');
        }
        return $name;
    }

    public function getFileName(): string
    {
        return $this->getDisplayName() . '.torrent';
    }
}
