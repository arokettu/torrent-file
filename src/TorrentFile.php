<?php

declare(strict_types=1);

namespace Arokettu\Torrent;

use Arokettu\Bencode\Types\BencodeSerializable;
use Arokettu\Torrent\DataTypes\Internal\DictObject;
use Arokettu\Torrent\FileSystem\FileData;
use DateTimeImmutable;
use DateTimeInterface;
use Psr\Clock\ClockInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

final class TorrentFile implements BencodeSerializable
{
    // main data field
    use TorrentFile\DataMethods;
    // save & load
    use TorrentFile\LoadMethods;
    use TorrentFile\StoreMethods;
    // signatures
    use TorrentFile\SignatureMethods;
    // fields
    use TorrentFile\Fields\StringFields;
    use TorrentFile\Fields\AnnounceList;
    use TorrentFile\Fields\CreationDate;
    use TorrentFile\Fields\HttpSeeds;
    use TorrentFile\Fields\Nodes;
    use TorrentFile\Fields\UrlList;
    // info manipulation
    use TorrentFile\InfoMethods;
    use TorrentFile\InfoFields\UpdateUrl;
    // file name suggestions
    use TorrentFile\NameMethods;
    // magnet link
    use TorrentFile\MagnetMethods;
    // handle v1 and v2 torrent data
    use TorrentFile\VersionMethods;

    public const CREATED_BY = 'Torrent File by Sand Fox https://sandfox.dev/php/torrent-file.html';

    private function __construct(DictObject $data)
    {
        $this->data = $data;
    }

    /**
     * Create torrent file for specified path
     */
    public static function fromPath(
        string $path,
        EventDispatcherInterface|null $eventDispatcher = null,
        MetaVersion|array $version = [MetaVersion::V1, MetaVersion::V2],
        int $pieceLength = 512 * 1024, // 512 KB
        bool|int $pieceAlign = false,
        bool $detectExec = true,
        bool $detectSymlinks = false,
        bool $forceMultifile = true,
        ClockInterface|null $clock = null, // todo: remove in 6.0
        DateTimeInterface|int|null $creationDate = null,
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
        $torrent->setCreationDate($creationDate ?? $clock?->now() ?? new DateTimeImmutable('now'));

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
}
