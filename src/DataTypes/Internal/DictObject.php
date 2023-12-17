<?php

declare(strict_types=1);

namespace Arokettu\Torrent\DataTypes\Internal;

use Arokettu\Bencode\Types\DictType;

/**
 * @internal
 * @template T
 * @implements StorageInterface<string, T>
 */
final class DictObject implements StorageInterface
{
    use ImmutableStorage;
    use DataObject;

    public function __construct(iterable $data)
    {
        $this->data = match (true) {
            $data instanceof DictObject => $data->data,
            // can be simply iterator_to_array in 8.2+
            \is_array($data) => $data,
            default => iterator_to_array($data),
        };
    }

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
