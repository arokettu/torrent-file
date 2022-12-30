<?php

declare(strict_types=1);

namespace Arokettu\Torrent\DataTypes\Internal;

use Arokettu\Bencode\Types\ListType;

/**
 * @internal
 * @implements StorageInterface<int, mixed>
 */
final class ListObject implements StorageInterface
{
    use ImmutableStorage;
    use DataObject;

    public function __construct(iterable $data)
    {
        // can be simply iterator_to_array in 8.2+
        $this->data = \is_array($data) ? array_values($data) : iterator_to_array($data, false);
    }

    public function bencodeSerialize(): ListType
    {
        return new ListType($this);
    }
}
