<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Info;

use Arokettu\Bencode\Bencode;
use Arokettu\Torrent\TorrentFile;
use PHPUnit\Framework\TestCase;

class FileNameSuggestionTest extends TestCase
{
    public function testDisplayName(): void
    {
        // basic

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['name' => 'my test torrent', 'pieces' => '']
        ]));

        self::assertEquals('my test torrent', $torrent->getDisplayName());
        self::assertEquals('my test torrent.torrent', $torrent->getFileName());

        // unicode

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['name' => 'トレント', 'pieces' => '']
        ]));

        self::assertEquals('トレント', $torrent->getDisplayName());
        self::assertEquals('トレント.torrent', $torrent->getFileName());

        // empty - use infohash

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['pieces' => ''],
        ]));

        self::assertEquals('d38308ebeda8a85e730b9393f0bb37970c57e78f', $torrent->getDisplayName());
        self::assertEquals('d38308ebeda8a85e730b9393f0bb37970c57e78f.torrent', $torrent->getFileName());

        // empty - use infohash v2

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['meta version' => 2],
        ]));

        self::assertEquals(
            '11f789319884160645bb421bfdfca60fac20c932cacea32c7757dd300a3765fd',
            $torrent->getDisplayName()
        );
        self::assertEquals(
            '11f789319884160645bb421bfdfca60fac20c932cacea32c7757dd300a3765fd.torrent',
            $torrent->getFileName()
        );
    }
}
