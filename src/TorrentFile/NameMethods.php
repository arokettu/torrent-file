<?php

declare(strict_types=1);

namespace SandFox\Torrent\TorrentFile;

/**
 * @internal
 */
trait NameMethods
{
    public function getDisplayName(): ?string
    {
        $infoName = $this->data['info']['name'] ?? '';

        return $infoName === '' ? $this->getInfoHash() : $infoName;
    }

    public function getFileName(): string
    {
        return $this->getDisplayName() . '.torrent';
    }
}
