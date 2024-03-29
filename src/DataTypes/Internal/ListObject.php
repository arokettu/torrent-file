<?php

declare(strict_types=1);

namespace Arokettu\Torrent\DataTypes\Internal;

use Arokettu\Bencode\Types\ListType;

/**
 * @internal
 * @template T
 * @implements StorageInterface<int, T>
 */
final class ListObject implements StorageInterface
{
    use ImmutableStorage;
    use DataObject;

    public function __construct(iterable $data)
    {
        $this->data = match (true) {
            $data instanceof ListObject => $data->data,
            // can be simply iterator_to_array in 8.2+
            \is_array($data) => array_values($data),
            default => iterator_to_array($data, false),
        };
    }

    public function bencodeSerialize(): ListType
    {
        return new ListType($this);
    }
}
