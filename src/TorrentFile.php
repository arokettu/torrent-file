<?php

declare(strict_types=1);

namespace SandFox\Torrent;

use ArrayObject;
use League\Uri\QueryString;
use Psr\EventDispatcher\EventDispatcherInterface;
use SandFox\Bencode\Bencode;
use SandFox\Bencode\Types\BencodeSerializable;
use SandFox\Torrent\Exception\InvalidArgumentException;
use SandFox\Torrent\FileSystem\FileData;

class TorrentFile implements BencodeSerializable
{
    public const CREATED_BY = 'PHP Torrent File by Sand Fox https://sandfox.dev/php/torrent-file.html';

    private array $data;

    // info hash cache
    private ?string $infoHash = null;

    /**
     * @param array $data
     * @internal Use named constructors instead
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Load data from torrent file
     *
     * @param string $fileName
     * @return TorrentFile
     */
    public static function load(string $fileName): self
    {
        return new self(Bencode::load($fileName, ['bigInt' => Bencode\BigInt::INTERNAL]));
    }

    /**
     * Load data from bencoded string
     *
     * @param string $string
     * @return TorrentFile
     */
    public static function loadFromString(string $string): self
    {
        return new self(Bencode::decode($string, ['bigInt' => Bencode\BigInt::INTERNAL]));
    }

    /**
     * Load data from bencoded stream
     *
     * @param resource $stream
     * @return TorrentFile
     */
    public static function loadFromStream($stream): self
    {
        return new self(Bencode::decodeStream($stream, ['bigInt' => Bencode\BigInt::INTERNAL]));
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
        $torrent->setCreationDate(time());

        return $torrent;
    }

    /**
     * Save torrent to file
     *
     * @param $fileName
     * @return bool
     */
    public function store($fileName): bool
    {
        return Bencode::dump($fileName, $this);
    }

    /**
     * Return torrent file as a string
     *
     * @return string
     */
    public function storeToString(): string
    {
        return Bencode::encode($this);
    }

    /**
     * Save torrent to a stream
     *
     * @param resource|null $stream
     * @return string
     */
    public function storeToStream($stream = null)
    {
        return Bencode::encodeToStream($this, $stream);
    }

    public function getRawData()
    {
        $filter = function ($value): bool {
            return $value !== null && $value !== [];
        };

        $rawData = $this->data;
        $rawData['info'] = array_filter($rawData['info'] ?? [], $filter);
        $rawData = array_filter($rawData, $filter);

        return $rawData;
    }

    public function bencodeSerialize()
    {
        return $this->getRawData();
    }

    /* Torrent file fields */

    public function setAnnounce(string $announce): self
    {
        $this->data['announce'] = $announce;
        return $this;
    }

    public function getAnnounce(): ?string
    {
        return $this->data['announce'] ?? null;
    }

    /**
     * @param string[]|string[][] $announceList
     * @return $this
     */
    public function setAnnounceList(array $announceList): self
    {
        foreach ($announceList as &$group) {
            if (is_string($group)) {
                $group = [$group];
                continue;
            }

            if (!is_array($group)) {
                throw new InvalidArgumentException(
                    'announce-list should be an array of strings or an array of arrays of strings'
                );
            }

            $group = array_unique($group);

            foreach ($group as $announce) {
                if (!is_string($announce)) {
                    throw new InvalidArgumentException(
                        'announce-list should be an array of strings or an array of arrays of strings'
                    );
                }
            }
        }

        $this->data['announce-list'] = array_values(
            array_unique(
                array_filter($announceList, 'count'),
                SORT_REGULAR
            )
        );

        return $this;
    }

    /**
     * @return string[][]
     */
    public function getAnnounceList(): array
    {
        return $this->data['announce-list'] ?? [];
    }

    public function setCreationDate(int $timestamp): self
    {
        $this->data['creation date'] = $timestamp;
        return $this;
    }

    public function getCreationDate(): ?int
    {
        return $this->data['creation date'] ?? null;
    }

    public function setComment(?string $comment): self
    {
        $this->data['comment'] = $comment;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->data['comment'] ?? null;
    }

    public function setCreatedBy(string $comment): self
    {
        $this->data['created by'] = $comment;
        return $this;
    }

    public function getCreatedBy(): string
    {
        return $this->data['created by'];
    }

    public function setPrivate(bool $isPrivate): self
    {
        $this->infoHash = null;

        if ($isPrivate) {
            $this->data['info']['private'] = 1;
        } else {
            unset($this->data['info']['private']);
        }

        return $this;
    }

    public function isPrivate(): bool
    {
        return isset($this->data['info']['private']) && $this->data['info']['private'] === 1;
    }

    /* service functions */

    public function getInfoHash(): string
    {
        return $this->infoHash ?? $this->infoHash = sha1(Bencode::encode(new ArrayObject($this->data['info'] ?? [])));
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
        $pairs = [];

        $dn = $this->data['info']['name'] ?? '';
        if ($dn !== '') {
            $pairs[] = ['dn', $this->getDisplayName()];
        }

        $pairs[] = ['xt', 'urn:btih:' . strtoupper($this->getInfoHash())];

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

        return 'magnet:?' . strval($query);
    }
}
