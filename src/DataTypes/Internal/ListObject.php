<?php

declare(strict_types=1);

namespace SandFox\Torrent\DataTypes\Internal;

use Arokettu\Bencode\Types\ListType;

/**
 * @internal
 * @implements StorageInterface<int, mixed>
 */
class ListObject implements StorageInterface
{
    use ImmutableStorage;
    use DataObject;

    public function bencodeSerialize(): ListType
    {
        return new ListType($this);
    }
}
