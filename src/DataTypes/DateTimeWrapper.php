<?php

declare(strict_types=1);

namespace Arokettu\Torrent\DataTypes;

use Arokettu\Bencode\Types\BencodeSerializable;
use DateTimeImmutable;

/**
 * Wrapper for nullable datetime
 *
 * @internal
 */
final class DateTimeWrapper implements BencodeSerializable
{
    private function __construct(
        public readonly DateTimeImmutable|null $dateTime
    ) {
    }

    /**
     * From the value that is hinted in setCreationDate()
     */
    public static function fromExternal(\DateTimeInterface|int|null $value): self
    {
        return match (true) {
            \is_null($value)
                => new self(null),
            \is_integer($value)
                => new self(new DateTimeImmutable('@' . $value)),
            default
                => new self(DateTimeImmutable::createFromInterface($value)),
        };
    }

    public static function fromInternal(int|null $value): self
    {
        // just narrows type checks
        return self::fromExternal($value);
    }

    public function bencodeSerialize(): int|null
    {
        return $this->dateTime?->getTimestamp();
    }
}
