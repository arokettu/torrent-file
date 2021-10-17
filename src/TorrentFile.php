<?php

declare(strict_types=1);

namespace SandFox\Torrent;

use Psr\EventDispatcher\EventDispatcherInterface;
use SandFox\Bencode\Decoder;
use SandFox\Bencode\Types\BencodeSerializable;
use SandFox\Torrent\FileSystem\FileData;

final class TorrentFile implements BencodeSerializable
{
    // save & load
    use TorrentFile\LoadMethods;
    use TorrentFile\StoreMethods;
    // fields
    use TorrentFile\Fields\StringFields;
    use TorrentFile\Fields\AnnounceList;
    use TorrentFile\Fields\CreationDate;
    // info manipulation
    use TorrentFile\InfoMethods;
    // file name suggestions
    use TorrentFile\NameMethods;
    // magnet link
    use TorrentFile\MagnetMethods;

    public const CREATED_BY = 'PHP Torrent File by Sand Fox https://sandfox.dev/php/torrent-file.html';

    private array $data;

    /**
     * @param array $data
     */
    private function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Create torrent file for specified path
     *
     * @param string $path to file or directory
     * @param array $options
     * @param EventDispatcherInterface|null $eventDispatcher Event Dispatcher to monitor hashing progress
     * @return TorrentFile
     */
    public static function fromPath(
        string $path,
        array $options = [],
        ?EventDispatcherInterface $eventDispatcher = null
    ): self {
        // generate data for files

        $dataGenerator = FileData::forPath($path, $options);

        $dataGenerator->generateData($eventDispatcher);

        $torrent = new self([
            'info' => $dataGenerator->getData(),
        ]);

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
        $rawData = (new Decoder())->decodeStream($stream);
        fclose($stream);

        return $rawData;
    }

    public function bencodeSerialize(): array
    {
        // clean data from empty arrays
        $filter = fn ($v) => $v !== [];

        $data = $this->data;
        $data = array_filter($data, $filter);

        return $data;
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
}
