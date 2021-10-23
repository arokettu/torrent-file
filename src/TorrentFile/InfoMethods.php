<?php

declare(strict_types=1);

namespace SandFox\Torrent\TorrentFile;

use SandFox\Bencode\Encoder;
use SandFox\Bencode\Types\DictType;
use SandFox\Torrent\Exception\InvalidArgumentException;

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

    public function setName(string $name): self
    {
        if ($name === '') {
            throw new InvalidArgumentException('$name must not be empty');
        }
        if (str_contains($name, '/') || str_contains($name, "\0")) {
            throw new InvalidArgumentException('$name must not contain slashes and zero bytes');
        }

        $this->infoHash = null;
        $this->data['info']['name'] = $name;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->data['info']['name'] ?? null;
    }

    public function getInfoHash(): string
    {
        return $this->infoHash ??= sha1((new Encoder())->encode(new DictType($this->data['info'] ?? [])));
    }
}
