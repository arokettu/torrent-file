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

    /**
     * @param \DateTimeInterface|int|null $timestamp
     */
    public function setCreationDate($timestamp): self
    {
        $this->creationDate = DateTimeWrapper::fromExternalValue($timestamp);
        $this->data['creation date'] = $this->creationDate;
        return $this;
    }

    /**
     * @deprecated Alias of getCreationDateAsTimestamp(). In 3.0 it will be an alias of getCreationDateAsDateTime()
     * @return int|null
     */
    public function getCreationDate(): ?int
    {
        trigger_deprecation(
            'sandfoxme/bencode',
            '2.2.0',
            'getCreationDate() will return an instance of DateTimeImmutable in 3.0. ' .
            'Use getCreationDateAsTimestamp() for future compatibility.'
        );
        return $this->getCreationDateAsTimestamp();
    }

    private function getCreationDateWrapper(): DateTimeWrapper
    {
        return $this->creationDate ??= DateTimeWrapper::fromTimestamp($this->data['creation date'] ?? null);
    }

    public function getCreationDateAsDateTime(): ?\DateTimeImmutable
    {
        return $this->getCreationDateWrapper()->getDateTime();
    }

    public function getCreationDateAsTimestamp(): ?int
    {
        return $this->getCreationDateWrapper()->getTimestamp();
    }
}
