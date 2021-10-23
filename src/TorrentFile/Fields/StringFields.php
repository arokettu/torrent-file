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
        $this->setField('announce', $announce);
        return $this;
    }

    public function getAnnounce(): ?string
    {
        return $this->getField('announce');
    }

    public function setComment(?string $comment): self
    {
        $this->setField('comment', $comment);
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->getField('comment');
    }

    public function setCreatedBy(?string $comment): self
    {
        $this->setField('created by', $comment);
        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->getField('created by');
    }
}
