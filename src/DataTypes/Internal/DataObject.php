<?php

declare(strict_types=1);

namespace SandFox\Torrent\DataTypes\Internal;

use Generator;

/**
 * @internal
 */
trait DataObject
{
    private readonly array $data;

    public function __construct(iterable $data)
    {
        // can be simply iterator_to_array in 8.2+
        $this->data = \is_array($data) ? $data : iterator_to_array($data);
    }

    public function getIterator(): Generator
    {
        yield from $this->data;
    }

    public function offsetGet(mixed $offset): mixed
    {
        // accessing undefined offset is legal for the raw data
        return $this->data[$offset] ?? null;
    }

    public function toArray(): array
    {
        $data = $this->data;

        foreach ($data as &$value) {
            if ($value instanceof StorageInterface) {
                $value = $value->toArray();
            }
        }

        return $data;
    }
}
