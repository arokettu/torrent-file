<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile;

use Arokettu\Bencode\Types\DictType;
use Arokettu\Torrent\DataTypes\Internal\InfoDict;
use Arokettu\Torrent\Exception\InvalidArgumentException;
use Arokettu\Torrent\MetaVersion;

/**
 * @internal
 */
trait InfoMethods
{
    // cached objects
    private ?InfoDict $info = null;

    abstract private function getField(string $key): mixed;
    abstract private function getInfoField(string $key): mixed;
    abstract private function setInfoField(string $key, mixed $value): void;

    public function setPrivate(bool $isPrivate): self
    {
        $this->setInfoField('private', $isPrivate);
        return $this;
    }

    public function isPrivate(): bool
    {
        return \boolval($this->getInfoField('private'));
    }

    public function setName(string $name): self
    {
        if ($name === '') {
            throw new InvalidArgumentException('$name must not be empty');
        }
        if (str_contains($name, '/') || str_contains($name, "\0")) {
            throw new InvalidArgumentException('$name must not contain slashes and zero bytes');
        }

        $this->setInfoField('name', $name);

        return $this;
    }

    public function getName(): ?string
    {
        return $this->getInfoField('name');
    }

    public function getInfoHashes(bool $binary = false): array
    {
        $hashes = [];

        if ($this->v1()) {
            $hashes[MetaVersion::V1->value] = $this->v1()->getInfoHash($binary);
        }

        if ($this->v2()) {
            $hashes[MetaVersion::V2->value] = $this->v2()->getInfoHash($binary);
        }

        return $hashes;
    }

    public function hasMetadata(MetaVersion $version): bool
    {
        return match ($version) {
            MetaVersion::V1 => $this->getInfoField('pieces') !== null,
            MetaVersion::V2 => $this->getInfoField('meta version') === 2,
        };
    }

    private function info(): InfoDict
    {
        return $this->info ??= new InfoDict($this->getField('info') ?? new DictType([]));
    }
}
