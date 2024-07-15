<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile\Fields;

/**
 * @internal
 */
trait StringFields
{
    abstract private function getField(string $key): mixed;
    abstract private function setField(string $key, mixed $value): void;

    public function setAnnounce(string|null $announce): self
    {
        $this->setField('announce', $announce);
        return $this;
    }

    public function getAnnounce(): string|null
    {
        return $this->getField('announce');
    }

    public function setComment(string|null $comment): self
    {
        $this->setField('comment', $comment);
        return $this;
    }

    public function getComment(): string|null
    {
        return $this->getField('comment');
    }

    public function setCreatedBy(string|null $comment): self
    {
        $this->setField('created by', $comment);
        return $this;
    }

    public function getCreatedBy(): string|null
    {
        return $this->getField('created by');
    }
}
