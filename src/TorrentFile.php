<?php

declare(strict_types=1);

namespace SandFox\Torrent;

use Psr\EventDispatcher\EventDispatcherInterface;
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

        // @codeCoverageIgnoreStart
        if (isset($options['sortFiles'])) {
            trigger_deprecation(
                'sandfoxme/torrent-file',
                '2.2',
                'sortFiles option is deprecated. Files are always sorted now',
            );
        }

        if (isset($options['md5sum'])) {
            trigger_deprecation(
                'sandfoxme/torrent-file',
                '2.2',
                'sortFiles option is deprecated. Files are always sorted now',
            );
        }
        // @codeCoverageIgnoreEnd

        $options = array_merge([
            'version'           => MetaVersion::V1,
            'pieceLength'       => 512 * 1024, // 512 KB
            'pieceAlign'        => false,
            'detectExec'        => true,
            'detectSymlinks'    => false,
            'forceMultifile'    => false,
        ], $options);

        if (\is_bool($options['pieceAlign'])) {
            $options['pieceAlign'] = $options['pieceAlign'] ? 0 : PHP_INT_MAX;
        }

        $dataGenerator = FileData::forPath(
            $path,
            $eventDispatcher,
            $options['version'],
            $options['pieceLength'],
            $options['pieceAlign'],
            $options['detectExec'],
            $options['detectSymlinks'],
            $options['forceMultifile'],
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

    /**
     * @param mixed $default
     * @return mixed
     */
    private function getField(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * @param mixed $value
     */
    private function setField(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    /**
     * @param mixed $default
     * @return mixed
     */
    private function getInfoField(string $key, $default = null)
    {
        return $this->data['info'][$key] ?? $default;
    }

    /**
     * @param mixed $value
     */
    private function setInfoField(string $key, $value): void
    {
        $this->infoHashV1 = null;
        $this->infoHashV2 = null;
        $this->data['info'][$key] = $value;
    }
}
