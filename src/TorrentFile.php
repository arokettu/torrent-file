<?php

declare(strict_types=1);

namespace Arokettu\Torrent;

use Arokettu\Bencode\Types\BencodeSerializable;
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

    private const CREATED_BY = 'Torrent File by Sand Fox https://sandfox.dev/php/torrent-file.html';

    private function __construct(
        private array $data,
    ) {}

    /**
     * Create torrent file for specified path
     */
    public static function fromPath(
        string $path,
        ?EventDispatcherInterface $eventDispatcher = null,
        MetaVersion $version = MetaVersion::HybridV1V2,
        int $pieceLength = 512 * 1024, // 512 KB
        bool|int $pieceAlign = false,
        bool $detectExec = true,
        bool $detectSymlinks = false,
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
        );

        $torrent = new self($dataGenerator->process());

        // set some defaults

        $torrent->setCreatedBy(self::CREATED_BY);
        $torrent->setCreationDate(new \DateTimeImmutable('now'));

        return $torrent;
    }

    public function getRawData(): array
    {
        $stream = fopen('php://temp', 'r+');
        $this->storeToStream($stream);
        rewind($stream);
        $rawData = self::decoder()->decodeStream($stream);
        fclose($stream);

        return $rawData;
    }

    public function bencodeSerialize(): array
    {
        return $this->data;
    }

    public function __serialize(): array
    {
        // normalize data on serialization
        return ['data' => $this->getRawData()];
    }

    public function __unserialize(array $data): void
    {
        ['data' => $this->data] = $data;
    }

    private function getField(string $key, mixed $default = null): mixed
    {
        return $this->data[$key] ?? $default;
    }

    private function setField(string $key, mixed $value): void
    {
        $this->data[$key] = $value;
    }

    private function getInfoField(string $key, mixed $default = null): mixed
    {
        return $this->data['info'][$key] ?? $default;
    }

    private function setInfoField(string $key, mixed $value): void
    {
        $this->infoString = null;
        $this->infoHashV1 = null;
        $this->infoHashV2 = null;
        $this->data['info'][$key] = $value;
    }
}
