<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile\Fields;

use Arokettu\Torrent\DataTypes\DateTimeWrapper;

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
        $this->creationDate ??= DateTimeWrapper::fromTimestamp($this->getField('creation date'));
        return $this->creationDate->dateTime;
    }

    /**
     * @deprecated use getCreationDate()
     */
    public function getCreationDateAsDateTime(): ?\DateTimeImmutable
    {
        trigger_deprecation(
            'sandfoxme/torrent-file',
            '3.0.0',
            'Use getCreationDate() instead of getCreationDateAsDateTime()',
        );
        return $this->getCreationDate();
    }

    /**
     * @deprecated use getCreationDate()->getTimestamp()
     */
    public function getCreationDateAsTimestamp(): ?int
    {
        trigger_deprecation(
            'sandfoxme/torrent-file',
            '3.0.0',
            'Use getCreationDate()->getTimestamp() instead of getCreationDateAsTimestamp()',
        );
        return $this->getCreationDate()?->getTimestamp();
    }
}
