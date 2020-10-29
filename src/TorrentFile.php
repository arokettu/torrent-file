<?php

namespace SandFox\Torrent;

use ArrayObject;
use SandFox\Bencode\Bencode;
use SandFox\Torrent\FileSystem\FileData;
use SandFox\Torrent\FileSystem\FileDataProgress;

class TorrentFile
{
    public const CREATED_BY = 'PHP Torrent File by Sand Fox https://sandfox.dev/php/torrent-file.html';

    private $data;

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
        return new self(Bencode::load($fileName));
    }

    /**
     * Load data from bencoded string
     *
     * @param string $string
     * @return string
     */
    public static function loadFromString(string $string): string
    {
        return new self(Bencode::decode($string));
    }

    /**
     * Create torrent file for specified path
     *
     * @param string $path to file or directory
     * @param array $options
     * @param FileDataProgress|null $progress Progress object to get hashing progress in a callback
     * @return TorrentFile
     */
    public static function fromPath(string $path, array $options = [], ?FileDataProgress $progress = null): self
    {
        // generate data for files

        $dataGenerator = FileData::forPath($path, $options);

        $dataGenerator->generateData($progress);

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
        return Bencode::dump($fileName, $this->getRawData());
    }

    /**
     * Return torrent file as a string
     *
     * @return string
     */
    public function storeToString(): string
    {
        return Bencode::encode($this->getRawData());
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

    public function setAnnounceList(array $announceList): self
    {
        if (count($announceList)) {
            $this->data['announce-list'] =  array_chunk($announceList, 1);
        } else {
            unset($this->data['announce-list']);
        }

        return $this;
    }

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

    public function setComment(string $comment): self
    {
        $this->data['comment'] = $comment;
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->data['comment'];
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
        return sha1(Bencode::encode(new ArrayObject($this->data['info'] ?? [])));
    }
}
