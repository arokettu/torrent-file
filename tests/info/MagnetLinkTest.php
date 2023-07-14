<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Info;

use Arokettu\Bencode\Bencode;
use Arokettu\Torrent\Exception\RuntimeException;
use Arokettu\Torrent\TorrentFile;
use PHPUnit\Framework\TestCase;

use function Arokettu\Torrent\Tests\build_magnet_link;

class MagnetLinkTest extends TestCase
{
    public function testDN(): void
    {
        // simple

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['name' => 'my test torrent', 'pieces' => '']
        ]));
        self::assertEquals(
            build_magnet_link([
                'xt=urn:btih:ea190bf5a23adbc91ea29062bf36a6a744b00436',
                'dn=my%20test%20torrent',
            ]),
            $torrent->getMagnetLink()
        );

        // unicode

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['name' => 'トレント', 'pieces' => '']
        ]));
        self::assertEquals(
            build_magnet_link([
                'xt=urn:btih:ded9d4a1598b648359de7665fa13b2714248723f',
                'dn=%E3%83%88%E3%83%AC%E3%83%B3%E3%83%88',
            ]),
            $torrent->getMagnetLink()
        );

        // empty

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['pieces' => '']
        ]));
        self::assertEquals('magnet:?xt=urn:btih:d38308ebeda8a85e730b9393f0bb37970c57e78f', $torrent->getMagnetLink());
    }

    public function testTR(): void
    {
        $base = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['name' => 'my test torrent', 'pieces' => '']
        ]));

        // add tracker

        $torrent = clone $base;
        $torrent->setAnnounce('http://example.com');
        self::assertEquals(
            build_magnet_link([
                'xt=urn:btih:ea190bf5a23adbc91ea29062bf36a6a744b00436',
                'dn=my%20test%20torrent',
                'tr=' . rawurlencode('http://example.com'),
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
                'xt=urn:btih:ea190bf5a23adbc91ea29062bf36a6a744b00436',
                'dn=my%20test%20torrent',
                'tr=' . rawurlencode('http://example.net'),
                'tr=' . rawurlencode('udp://example.org:4321'),
                'tr=' . rawurlencode('http://example.org'),
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
                'xt=urn:btih:ea190bf5a23adbc91ea29062bf36a6a744b00436',
                'dn=my%20test%20torrent',
                'tr=' . rawurlencode('http://example.com'),
                'tr=' . rawurlencode('http://example.net'),
                'tr=' . rawurlencode('udp://example.org:4321'),
                'tr=' . rawurlencode('http://example.org'),
            ]),
            $torrent->getMagnetLink()
        );
    }

    public function testInfoHashes(): void
    {
        // v1

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['pieces' => '']
        ]));
        self::assertEquals(
            'magnet:?xt=urn:btih:d38308ebeda8a85e730b9393f0bb37970c57e78f',
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
            'info' => ['meta version' => 2, 'pieces' => '']
        ]));
        self::assertEquals(build_magnet_link([
            'xt=urn:btih:810c7a83166568622e1712c14410243cd836d31c',
            'xt=urn:btmh:12204bbc2b7563b4ec457114b60a477c5d5775a0f80de5ed6fe3173067ca1109f604',
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
