<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile;

use Arokettu\Bencode\Encoder;

/**
 * @internal
 */
trait StoreMethods
{
    private static function encoder(): Encoder
    {
        return new Encoder();
    }

    /**
     * Save torrent to file
     */
    public function store(string $fileName): bool
    {
        return self::encoder()->dump($this, $fileName);
    }

    /**
     * Return torrent file as a string
     */
    public function storeToString(): string
    {
        return self::encoder()->encode($this);
    }

    /**
     * Save torrent to a stream
     *
     * @param resource|null $stream
     * @return resource
     */
    public function storeToStream($stream = null)
    {
        return self::encoder()->encodeToStream($this, $stream);
    }

    public function __serialize(): array
    {
        // normalize data on serialization
        return ['bin' => $this->storeToString()];
    }
}
