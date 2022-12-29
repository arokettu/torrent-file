<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile;

use Arokettu\Bencode\Bencode\BigInt;
use Arokettu\Bencode\Decoder;
use SandFox\Torrent\DataTypes\Internal\DictObject;
use SandFox\Torrent\DataTypes\Internal\ListObject;

/**
 * @internal
 */
trait LoadMethods
{
    abstract private function __construct(DictObject $data);

    private static function decoder(): Decoder
    {
        return new Decoder(
            listType: static fn (iterable $list) => new ListObject($list),
            dictType: static fn (iterable $dict) => new DictObject($dict),
            bigInt: BigInt::INTERNAL,
        );
    }

    /**
     * Load data from torrent file
     */
    public static function load(string $fileName): self
    {
        return new self(self::decoder()->load($fileName));
    }

    /**
     * Load data from bencoded string
     */
    public static function loadFromString(string $string): self
    {
        return new self(self::decoder()->decode($string));
    }

    /**
     * Load data from bencoded stream
     * @param resource $stream
     */
    public static function loadFromStream($stream): self
    {
        return new self(self::decoder()->decodeStream($stream));
    }
}
