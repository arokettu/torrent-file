<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\All;

use PHPUnit\Framework\TestCase;
use SandFox\Bencode\Bencode;
use SandFox\Torrent\TorrentFile;

class AuxFieldsTest extends TestCase
{
    public function testDisplayName(): void
    {
        // basic

        $torrent = TorrentFile::loadFromString(Bencode::encode(['info' => ['name' => 'my test torrent']]));

        self::assertEquals('my test torrent', $torrent->getDisplayName());
        self::assertEquals('my test torrent.torrent', $torrent->getFileName());

        // unicode

        $torrent = TorrentFile::loadFromString(Bencode::encode(['info' => ['name' => 'トレント']]));

        self::assertEquals('トレント', $torrent->getDisplayName());
        self::assertEquals('トレント.torrent', $torrent->getFileName());

        // empty - use infohash

        $torrent = TorrentFile::loadFromString('de');

        self::assertEquals('600ccd1b71569232d01d110bc63e906beab04d8c', $torrent->getDisplayName());
        self::assertEquals('600ccd1b71569232d01d110bc63e906beab04d8c.torrent', $torrent->getFileName());
    }
}
