<?php

declare(strict_types=1);

namespace SandFox\Torrent;

use ArrayObject;
use League\Uri\QueryString;
use Psr\EventDispatcher\EventDispatcherInterface;
use SandFox\Bencode\Decoder;
use SandFox\Bencode\Encoder;
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

    public const CREATED_BY = 'PHP Torrent File by Sand Fox https://sandfox.dev/php/torrent-file.html';

    private array $data;

    // info hash cache
    private ?string $infoHash = null;

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
        $data['info'] = array_filter($data['info'] ?? [], $filter);
        $data = array_filter($data, $filter);

        return $data;
    }

    // private flag BEP-0027

    public function setPrivate(bool $isPrivate): self
    {
        $this->infoHash = null;
        $this->data['info']['private'] = $isPrivate;

        return $this;
    }

    public function isPrivate(): bool
    {
        return \boolval($this->data['info']['private'] ?? false);
    }

    /* service functions */

    public function getInfoHash(): string
    {
        return $this->infoHash ??= sha1((new Encoder())->encode(new ArrayObject($this->data['info'] ?? [])));
    }

    public function getDisplayName(): ?string
    {
        $infoName = $this->data['info']['name'] ?? '';

        return $infoName === '' ? $this->getInfoHash() : $infoName;
    }

    public function getFileName(): string
    {
        return $this->getDisplayName() . '.torrent';
    }

    public function getMagnetLink(): string
    {
        $pairs = [['xt', 'urn:btih:' . strtoupper($this->getInfoHash())]];

        $dn = $this->data['info']['name'] ?? '';
        if ($dn !== '') {
            $pairs[] = ['dn', $this->getDisplayName()];
        }

        $trackers = [];

        $rootTracker = $this->getAnnounce();

        if ($rootTracker) {
            $trackers[] = $rootTracker;
        }

        foreach ($this->getAnnounceList() as $trGroup) {
            foreach ($trGroup as $tracker) {
                $trackers[] = $tracker;
            }
        }

        foreach (array_unique($trackers) as $tr) {
            $pairs[] = ['tr', $tr];
        }

        $query = QueryString::build($pairs);

        return 'magnet:?' . $query;
    }

    // handle serialization

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
