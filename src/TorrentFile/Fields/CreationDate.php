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
        $this->creationDate = DateTimeWrapper::fromExternal($timestamp);
        $this->setField('creation date', $this->creationDate);
        return $this;
    }

    public function getCreationDate(): ?\DateTimeImmutable
    {
        $this->creationDate ??= DateTimeWrapper::fromInternal($this->getField('creation date'));
        return $this->creationDate->dateTime;
    }
}
