<?php

declare(strict_types=1);

namespace Arokettu\Torrent;

use Arokettu\Bencode\Types\BencodeSerializable;
use Arokettu\Torrent\DataTypes\Internal\DictObject;
use Arokettu\Torrent\DataTypes\Internal\Undefined;
use Arokettu\Torrent\FileSystem\FileData;
use Psr\EventDispatcher\EventDispatcherInterface;

final class TorrentFile implements BencodeSerializable
{
    // save & load
    use TorrentFile\LoadMethods;
    use TorrentFile\StoreMethods;
    // fields
    use TorrentFile\Fields\StringFields;
    use TorrentFile\Fields\AnnounceList;
    use TorrentFile\Fields\CreationDate;
    use TorrentFile\Fields\HttpSeeds;
    use TorrentFile\Fields\Nodes;
    use TorrentFile\Fields\UrlList;
    // info manipulation
    use TorrentFile\InfoMethods;
    // file name suggestions
    use TorrentFile\NameMethods;
    // magnet link
    use TorrentFile\MagnetMethods;
    // handle v1 and v2 torrent data
    use TorrentFile\VersionMethods;

    private const CREATED_BY = 'Torrent File by Sand Fox https://sandfox.dev/php/torrent-file.html';

    private function __construct(
        private DictObject $data,
    ) {}

    /**
     * Create torrent file for specified path
     */
    public static function fromPath(
        string $path,
        ?EventDispatcherInterface $eventDispatcher = null,
        MetaVersion|array $version = [MetaVersion::V1, MetaVersion::V2],
        int $pieceLength = 512 * 1024, // 512 KB
        bool|int $pieceAlign = false,
        bool $detectExec = true,
        bool $detectSymlinks = false,
        bool $forceMultifile = false,
    ): self {
        // generate data for files

        if (\is_bool($pieceAlign)) {
            $pieceAlign = $pieceAlign ? 0 : PHP_INT_MAX;
        }

        $dataGenerator = FileData::forPath(
            $path,
            $eventDispatcher,
            $version,
            $pieceLength,
            $pieceAlign,
            $detectExec,
            $detectSymlinks,
            $forceMultifile,
        );

        $torrent = new self(self::decoder()->decode($dataGenerator->getBencoded()));

        // set some defaults

        $torrent->setCreatedBy(self::CREATED_BY);
        $torrent->setCreationDate(new \DateTimeImmutable('now'));

        return $torrent;
    }

    public function getRawData(): DictObject
    {
        $stream = fopen('php://temp', 'r+');
        $this->storeToStream($stream);
        rewind($stream);
        $rawData = self::decoder()->decodeStream($stream);
        fclose($stream);

        return $rawData;
    }

    public function bencodeSerialize(): DictObject
    {
        return $this->data;
    }

    public function __serialize(): array
    {
        // normalize data on serialization
        return ['bin' => $this->storeToString()];
    }

    public function __unserialize(array $data): void
    {
        $this->data = self::decoder()->decode($data['bin']);
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
        $this->info = null;
        $this->v1 = Undefined::Undefined;
        $this->v2 = Undefined::Undefined;
        $info = $this->data['info'] ?? new DictObject([]); // enforce info to be a dictionary
        $info = $info->withOffset($key, $value);
        $this->data = $this->data->withOffset('info', $info);
    }
}
