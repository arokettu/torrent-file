<?php

declare(strict_types=1);

namespace SandFox\Torrent\DataTypes;

use SandFox\Bencode\Types\BencodeSerializable;

/**
 * Wrapper for nullable datetime
 *
 * @internal
 */
final class DateTimeWrapper implements BencodeSerializable
{
    private ?\DateTimeImmutable $dateTime;

    public function __construct(?\DateTimeImmutable $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    /**
     * From the value that is hinted in setCreationDate()
     */
    public static function fromExternalValue(\DateTimeInterface|int|null $value): self
    {
        if (\is_integer($value)) {
            return self::fromTimestamp($value);
        }

        return self::fromDateTime($value);
    }

    public static function fromTimestamp(?int $timestamp): self
    {
        return new self($timestamp !== null ? new \DateTimeImmutable('@' . $timestamp) : null);
    }

    public static function fromDateTime(?\DateTimeInterface $dateTime): self
    {
        return new self($dateTime ? \DateTimeImmutable::createFromInterface($dateTime) : null);
    }

    public function getDateTime(): ?\DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function getTimestamp(): ?int
    {
        return $this->dateTime?->getTimestamp();
    }

    public function bencodeSerialize(): ?int
    {
        return $this->getTimestamp();
    }
}
