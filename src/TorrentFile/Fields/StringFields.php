<?php

declare(strict_types=1);

namespace SandFox\Torrent\TorrentFile\Fields;

/**
 * @internal
 */
trait StringFields
{
    public function setAnnounce(?string $announce): self
    {
        $this->data['announce'] = $announce;
        return $this;
    }

    public function getAnnounce(): ?string
    {
        return $this->data['announce'] ?? null;
    }

    public function setComment(?string $comment): self
    {
        $this->data['comment'] = $comment;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->data['comment'] ?? null;
    }

    public function setCreatedBy(?string $comment): self
    {
        $this->data['created by'] = $comment;
        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->data['created by'] ?? null;
    }
}
