<?php

declare(strict_types=1);

namespace SandFox\Torrent\TorrentFile;

/**
 * @internal
 */
trait NameMethods
{
    abstract public function getInfoHash(): string;

    public function getDisplayName(): string
    {
        $name = $this->getName() ?? '';
        return $name === '' ? $this->getInfoHash() : $name;
    }

    public function getFileName(): string
    {
        return $this->getDisplayName() . '.torrent';
    }
}
