<?php

namespace SandFox\Torrent;

use ArrayObject;
use SandFox\Bencode\Bencode;
use SandFox\Bencode\Types\ListType;
use SandFox\Torrent\FileSystem\FileData;
use SandFox\Torrent\FileSystem\FileDataProgress;

class TorrentFile
{
    public const CREATED_BY = 'PHP Torrent File by Sand Fox https://sandfox.dev/php/torrent-file.html';

    private $data;

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
     * Create torrent file for specified path
     *
     * @param string $path to file or directory
     * @param FileDataProgress|null $progress Progress object to get hashing progress in a callback
     * @return TorrentFile
     */
    public static function fromPath($path, ?FileDataProgress $progress = null): self
    {
        // generate data for files

        $dataGenerator = FileData::forPath($path);

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
        return Bencode::dump($fileName, $this->data);
    }

    public function getRawData()
    {
        return $this->data;
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
        $this->data['announce-list'] = new ListType($announceList);

        return $this;
    }

    public function getAnnounceList(): ?array
    {
        return $this->data['announce-list'] ?? null;
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
