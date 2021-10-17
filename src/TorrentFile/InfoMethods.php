<?php

declare(strict_types=1);

namespace SandFox\Torrent\TorrentFile;

use SandFox\Bencode\Encoder;
use SandFox\Bencode\Types\DictType;

/**
 * @internal
 */
trait InfoMethods
{
    // info hash cache
    private ?string $infoHash = null;

    public function setPrivate(bool $isPrivate): self
    {
        $this->infoHash = null;
        $this->data['info']['private'] = $isPrivate;

        return $this;
    }

    public function isPrivate(): bool
    {
        return \boolval($this->data['info']['private'] ?? false);
    }

    public function getInfoHash(): string
    {
        return $this->infoHash ??= sha1((new Encoder())->encode(new DictType($this->data['info'] ?? [])));
    }
}
