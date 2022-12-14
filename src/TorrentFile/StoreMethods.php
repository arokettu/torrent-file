<?php

declare(strict_types=1);

namespace SandFox\Torrent\TorrentFile;

use Arokettu\Bencode\Encoder;

/**
 * @internal
 */
trait StoreMethods
{
    /**
     * Save torrent to file
     */
    public function store(string $fileName): bool
    {
        return (new Encoder())->dump($this, $fileName);
    }

    /**
     * Return torrent file as a string
     */
    public function storeToString(): string
    {
        return (new Encoder())->encode($this);
    }

    /**
     * Save torrent to a stream
     *
     * @param resource|null $stream
     * @return resource
     */
    public function storeToStream($stream = null)
    {
        return (new Encoder())->encodeToStream($this, $stream);
    }
}
