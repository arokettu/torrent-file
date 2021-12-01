<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\Info;

use PHPUnit\Framework\TestCase;
use SandFox\Bencode\Bencode;
use SandFox\Torrent\Exception\RuntimeException;
use SandFox\Torrent\TorrentFile;

use function SandFox\Torrent\Tests\build_magnet_link;

class MagnetLinkTest extends TestCase
{
    public function testDN(): void
    {
        // simple

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['name' => 'my test torrent', 'length' => 0]
        ]));
        self::assertEquals(
            build_magnet_link([
                'xt=urn:btih:8cdf7e5bdf89ebb3e33f3afe66362e99556cf8d3',
                'dn=my%20test%20torrent',
            ]),
            $torrent->getMagnetLink()
        );

        // unicode

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['name' => 'トレント', 'length' => 0]
        ]));
        self::assertEquals(
            build_magnet_link([
                'xt=urn:btih:671ea756e405e74b7fe2710076ae9056cc19b69c',
                'dn=%E3%83%88%E3%83%AC%E3%83%B3%E3%83%88',
            ]),
            $torrent->getMagnetLink()
        );

        // empty

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['length' => 0]
        ]));
        self::assertEquals('magnet:?xt=urn:btih:26f0b584fa6fea9ccc2c627f8f6df9feb752ed96', $torrent->getMagnetLink());
    }

    public function testTR(): void
    {
        $base = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['name' => 'my test torrent', 'length' => 0]
        ]));

        // add tracker

        $torrent = clone $base;
        $torrent->setAnnounce('http://example.com');
        self::assertEquals(
            build_magnet_link([
                'xt=urn:btih:8cdf7e5bdf89ebb3e33f3afe66362e99556cf8d3',
                'dn=my%20test%20torrent',
                'tr=http://example.com',
            ]),
            $torrent->getMagnetLink()
        );

        // add list of trackers

        $torrent = clone $base;
        $torrent->setAnnounceList([
            'http://example.net',
            ['udp://example.org:4321', 'http://example.org']
        ]);
        self::assertEquals(
            build_magnet_link([
                'xt=urn:btih:8cdf7e5bdf89ebb3e33f3afe66362e99556cf8d3',
                'dn=my%20test%20torrent',
                'tr=http://example.net',
                'tr=udp://example.org:4321',
                'tr=http://example.org',
            ]),
            $torrent->getMagnetLink()
        );

        // mixed

        $torrent = clone $base;
        $torrent->setAnnounce('http://example.com');
        $torrent->setAnnounceList([
            'http://example.net',
            'http://example.com', // removes duplicates that are allowed in base class
            ['udp://example.org:4321', 'http://example.org', 'http://example.net']
        ]);
        self::assertEquals(
            build_magnet_link([
                'xt=urn:btih:8cdf7e5bdf89ebb3e33f3afe66362e99556cf8d3',
                'dn=my%20test%20torrent',
                'tr=http://example.com',
                'tr=http://example.net',
                'tr=udp://example.org:4321',
                'tr=http://example.org',
            ]),
            $torrent->getMagnetLink()
        );
    }

    public function testInfoHashes(): void
    {
        // v1

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['length' => 0]
        ]));
        self::assertEquals(
            'magnet:?xt=urn:btih:26f0b584fa6fea9ccc2c627f8f6df9feb752ed96',
            $torrent->getMagnetLink()
        );

        // v2

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['meta version' => 2]
        ]));
        self::assertEquals(
            'magnet:?xt=urn:btmh:122011f789319884160645bb421bfdfca60fac20c932cacea32c7757dd300a3765fd',
            $torrent->getMagnetLink()
        );

        // v1 + v2

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['meta version' => 2, 'length' => 0]
        ]));
        self::assertEquals(build_magnet_link([
            'xt=urn:btih:e5c50f1621e46db4b5356e3634ba80a5a4984244',
            'xt=urn:btmh:122097df733df47fd30c2f0fe280eeff81114d69d2d0b6bb8c1f314a9eb52a5bc033',
        ]), $torrent->getMagnetLink());
    }

    public function testValidMetadataRequired(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Trying to create a magnet link for a file without valid metadata');

        $torrent = TorrentFile::loadFromString('de');
        $torrent->getMagnetLink();
    }
}
