<?php

declare(strict_types=1);

namespace SandFox\Torrent\TorrentFile;

use SandFox\Bencode\Bencode\BigInt;
use SandFox\Bencode\Decoder;

/**
 * @internal
 */
trait LoadMethods
{
    abstract private function __construct(array $data = []);

    private static function decoder(): Decoder
    {
        return new Decoder(['bigInt' => BigInt::INTERNAL]);
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
