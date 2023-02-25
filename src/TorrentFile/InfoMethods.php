<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile;

use Arokettu\Bencode\Encoder;
use Arokettu\Bencode\Types\DictType;
use Arokettu\Torrent\Exception\InvalidArgumentException;
use Arokettu\Torrent\MetaVersion;

/**
 * @internal
 */
trait InfoMethods
{
    // info hash cache
    // null = not calculated, '' = not present (returned to user as null)
    private ?string $infoHashV1 = null;
    private ?string $infoHashV2 = null;
    private ?string $infoString = null;

    abstract private function getField(string $key, mixed $default = null): mixed;
    abstract private function getInfoField(string $key, mixed $default = null): mixed;
    abstract private function setInfoField(string $key, mixed $value): void;

    public function setPrivate(bool $isPrivate): self
    {
        $this->setInfoField('private', $isPrivate);
        return $this;
    }

    public function isPrivate(): bool
    {
        return \boolval($this->getInfoField('private', false));
    }

    public function isDirectory(): bool
    {
        // v1
        if ($this->getInfoField('length') !== null) {
            return false;
        }

        $files = $this->getInfoField('files');
        if ($files !== null) {
            return \count($files) > 1 || \count($files[0]['path']) > 1;
        }

        // v2
        if ($this->getInfoField('meta version') === 2) {
            $fileTree = $this->getInfoField('file tree');

            if (\count($fileTree) !== 1) {
                return true;
            }

            // check first file
            foreach ($fileTree as $file) {
                if (isset($file['']['length'])) {
                    return false;
                }
                // @codeCoverageIgnoreStart
                // should never happen, see below
                break;
                // @codeCoverageIgnoreEnd
            }
        }

        // @codeCoverageIgnoreStart
        // should never happen
        throw new \LogicException('Unable to determine');
        // @codeCoverageIgnoreEnd
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

    private function getInfoString(): string
    {
        return $this->infoString ??= (new Encoder())->encode(
            new DictType(
                $this->getField('info', new DictType())
            )
        );
    }

    public function getInfoHash(MetaVersion $version, bool $binary = false): ?string
    {
        return match ($version) {
            MetaVersion::V1 =>
                $this->getInfoHashV1($binary),
            MetaVersion::V2 =>
                $this->getInfoHashV2($binary),
        };
    }

    public function getInfoHashes(bool $binary = false): array
    {
        $hashes = [];

        $v1 = $this->getInfoHash(version: MetaVersion::V1, binary: $binary);
        if ($v1) {
            $hashes[MetaVersion::V1->value] = $v1;
        }

        $v2 = $this->getInfoHash(version: MetaVersion::V2, binary: $binary);
        if ($v2) {
            $hashes[MetaVersion::V2->value] = $v2;
        }

        return $hashes;
    }

    private function getInfoHashV1(bool $binary = false): ?string
    {
        $this->infoHashV1 ??= $this->calcInfoHashV1();

        // empty string means that there is no hash, return null
        if ($this->infoHashV1 === '') {
            return null;
        }

        return $binary ? $this->infoHashV1 : bin2hex($this->infoHashV1);
    }

    private function calcInfoHashV1(): string
    {
        $info = $this->getField('info', []);

        if (isset($info['pieces'])) {
            // v1 metadata found
            return sha1($this->getInfoString(), true);
        }

        return '';
    }

    private function getInfoHashV2(bool $binary = false): ?string
    {
        $this->infoHashV2 ??= $this->calcInfoHashV2();

        // empty string means that there is no hash, return null
        if ($this->infoHashV2 === '') {
            return null;
        }

        return $binary ? $this->infoHashV2 : bin2hex($this->infoHashV2);
    }

    private function calcInfoHashV2(): string
    {
        $version = $this->getInfoField('meta version');

        if ($version === 2) {
            // trust the version declaration
            return hash('sha256', $this->getInfoString(), true);
        }

        return '';
    }

    public function hasMetadata(MetaVersion $version): bool
    {
        return match ($version) {
            MetaVersion::V1 => $this->getInfoField('pieces') !== null,
            MetaVersion::V2 => $this->getInfoField('meta version') === 2,
        };
    }
}
