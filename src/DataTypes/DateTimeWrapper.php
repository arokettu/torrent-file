<?php

declare(strict_types=1);

namespace Arokettu\Torrent\DataTypes;

use Arokettu\Bencode\Types\BencodeSerializable;

/**
 * Wrapper for nullable datetime
 *
 * @internal
 */
final class DateTimeWrapper implements BencodeSerializable
{
    public function __construct(
        public readonly ?\DateTimeImmutable $dateTime
    ) {}

    /**
     * From the value that is hinted in setCreationDate()
     */
    public static function fromExternalValue(\DateTimeInterface|int|null $value): self
    {
        return match (true) {
            \is_integer($value)
                => self::fromTimestamp($value),
            default
                => self::fromDateTime($value),
        };
    }

    public static function fromTimestamp(?int $timestamp): self
    {
        return new self($timestamp !== null ? new \DateTimeImmutable('@' . $timestamp) : null);
    }

    public static function fromDateTime(?\DateTimeInterface $dateTime): self
    {
        return new self($dateTime ? \DateTimeImmutable::createFromInterface($dateTime) : null);
    }

    public function bencodeSerialize(): ?int
    {
        return $this->dateTime?->getTimestamp();
    }
}
