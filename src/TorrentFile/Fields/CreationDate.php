<?php

declare(strict_types=1);

namespace SandFox\Torrent\TorrentFile\Fields;

use SandFox\Torrent\DataTypes\DateTimeWrapper;

/**
 * @internal
 */
trait CreationDate
{
    private ?DateTimeWrapper $creationDate = null;

    abstract private function getField(string $key, mixed $default = null): mixed;
    abstract private function setField(string $key, mixed $value): void;

    public function setCreationDate(\DateTimeInterface|int|null $timestamp): self
    {
        $this->creationDate = DateTimeWrapper::fromExternalValue($timestamp);
        $this->setField('creation date', $this->creationDate);
        return $this;
    }

    public function getCreationDate(): ?\DateTimeImmutable
    {
        return $this->getCreationDateAsTimestamp();
    }

    /**
     * @deprecated use getCreationDate()
     */
    public function getCreationDateAsDateTime(): ?\DateTimeImmutable
    {
        return $this->getCreationDate();
    }

    /**
     * @deprecated use getCreationDate()->getTimestamp()
     */
    public function getCreationDateAsTimestamp(): ?int
    {
        return $this->getCreationDate()?->getTimestamp();
    }
}
