<?php

declare(strict_types=1);

namespace SandFox\Torrent\DataTypes\Internal;

use Arokettu\Bencode\Types\DictType;

/**
 * @internal
 * @implements StorageInterface<string, mixed>
 */
class DictObject implements StorageInterface
{
    use ImmutableStorage;
    use DataObject;

    public function bencodeSerialize(): DictType
    {
        return new DictType($this);
    }

    public function withOffset(string $key, mixed $value): self
    {
        $data = $this->data;
        if ($value === null || $value === false) {
            unset($data[$key]); // null and false values are not stored
        } else {
            $data[$key] = $value;
        }

        return new self($data);
    }
}