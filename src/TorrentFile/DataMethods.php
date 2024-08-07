<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile;

use Arokettu\Torrent\DataTypes\Internal\DictObject;
use Arokettu\Torrent\Exception\BadMethodCallException;

/**
 * @internal
 */
trait DataMethods
{
    private DictObject $data;

    abstract private function resetInfoDict(): void;
    abstract private function resetCachedVersionObjects(): void;
    abstract public function isSigned(): bool;

    public function bencodeSerialize(): DictObject
    {
        return $this->data;
    }

    private function getField(string $key): mixed
    {
        return $this->data[$key];
    }

    private function setField(string $key, mixed $value): void
    {
        $this->data = $this->data->withOffset($key, $value);
    }

    private function getInfoField(string $key): mixed
    {
        return ($this->data['info'] ?? new DictObject([]))[$key];
    }

    private function setInfoField(string $key, mixed $value): void
    {
        if ($this->isSigned()) {
            throw new BadMethodCallException(
                'Unable to modify infohash fields of a signed torrent. ' .
                'Please remove the signatures first'
            );
        }

        $this->resetInfoDict();
        $this->resetCachedVersionObjects();
        $info = $this->data['info'] ?? new DictObject([]); // enforce info to be a dictionary
        $info = $info->withOffset($key, $value);
        $this->data = $this->data->withOffset('info', $info);
    }
}
