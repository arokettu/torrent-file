<?php

declare(strict_types=1);

namespace SandFox\Torrent\TorrentFile\Fields;

/**
 * @internal
 */
trait Announce
{
    public function setAnnounce(string $announce): self
    {
        $this->data['announce'] = $announce;
        return $this;
    }

    public function getAnnounce(): ?string
    {
        return $this->data['announce'] ?? null;
    }
}
