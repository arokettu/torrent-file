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
    abstract private function setField(string $key, mixed $value): void;
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

    /**
     * @return list<MetaVersion>
     */
    public function getMetadataVersions(): array
    {
        $versions = [];
        foreach (MetaVersion::cases() as $version) {
            if ($this->hasMetadata($version)) {
                $versions[] = $version;
            }
        }
        return $versions;
    }

    private function info(): InfoDict
    {
        return $this->info ??= new InfoDict($this->getField('info') ?? new DictType([]));
    }

    private function resetInfoDict(): void
    {
        $this->info = null;
    }

    public function removeMetadata(MetaVersion $version): void
    {
        if (array_diff($this->getMetadataVersions(), [$version]) === []) {
            throw new InvalidArgumentException('Unable to remove the only remaining metadata');
        }

        match ($version) {
            MetaVersion::V1 => $this->eraseV1(),
            MetaVersion::V2 => $this->eraseV2(),
        };
    }

    public function keepOnlyMetadata(MetaVersion $version): void
    {
        if (!$this->hasMetadata($version)) {
            throw new InvalidArgumentException('Unable to keep metadata that is not present');
        }

        match ($version) {
            MetaVersion::V1 => $this->eraseV2(),
            MetaVersion::V2 => $this->eraseV1(),
        };
    }

    private function eraseV1(): void
    {
        // main v1 field
        $this->setInfoField('pieces', null);
        // multifile
        $this->setInfoField('files', null);
        // single file
        $this->setInfoField('length', null);
        $this->setInfoField('attr', null);
        $this->setInfoField('sha1', null);

        // keep piece length and name
    }

    private function eraseV2(): void
    {
        $this->setInfoField('meta version', null);
        $this->setInfoField('file tree', null);
        $this->setField('piece layers', null);
    }
}
