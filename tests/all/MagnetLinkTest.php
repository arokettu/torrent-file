<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\All;

use PHPUnit\Framework\TestCase;
use SandFox\Bencode\Bencode;
use SandFox\Torrent\TorrentFile;

class MagnetLinkTest extends TestCase
{
    public function testDN()
    {
        // simple

        $torrent = TorrentFile::loadFromString(Bencode::encode(['info' => ['name' => 'my test torrent']]));
        $this->assertEquals(
            'magnet:?xt=urn:btih:bf88dd1fdfdd0e8b596e6aa39ebea83d536f1dde&dn=my%20test%20torrent',
            $torrent->getMagnetLink()
        );

        // unicode

        $torrent = TorrentFile::loadFromString(Bencode::encode(['info' => ['name' => 'トレント']]));
        $this->assertEquals(
            'magnet:?xt=urn:btih:f8a6ef35f6b70e8e599ceb2ce3de920111524fba&dn=%E3%83%88%E3%83%AC%E3%83%B3%E3%83%88',
            $torrent->getMagnetLink()
        );

        // empty

        $torrent = TorrentFile::loadFromString('de');
        $this->assertEquals('magnet:?xt=urn:btih:600ccd1b71569232d01d110bc63e906beab04d8c', $torrent->getMagnetLink());
    }

    public function testTR()
    {
        $base = TorrentFile::loadFromString(Bencode::encode(['info' => ['name' => 'my test torrent']]));

        // add tracker

        $torrent = clone $base;
        $torrent->setAnnounce('http://example.com');
        $this->assertEquals(
            'magnet:?xt=urn:btih:bf88dd1fdfdd0e8b596e6aa39ebea83d536f1dde' .
                '&dn=my%20test%20torrent' .
                '&tr=http%3A%2F%2Fexample.com',
            $torrent->getMagnetLink()
        );

        // add list of trackers

        $torrent = clone $base;
        $torrent->setAnnounceList([
            'http://example.net',
            ['udp://example.org:4321', 'http://example.org']
        ]);
        $this->assertEquals(
            'magnet:?xt=urn:btih:bf88dd1fdfdd0e8b596e6aa39ebea83d536f1dde' .
                '&dn=my%20test%20torrent&tr=http%3A%2F%2Fexample.net' .
                '&tr=udp%3A%2F%2Fexample.org%3A4321' .
                '&tr=http%3A%2F%2Fexample.org',
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
        $this->assertEquals(
            'magnet:?xt=urn:btih:bf88dd1fdfdd0e8b596e6aa39ebea83d536f1dde' .
                '&dn=my%20test%20torrent' .
                '&tr=http%3A%2F%2Fexample.com' .
                '&tr=http%3A%2F%2Fexample.net' .
                '&tr=udp%3A%2F%2Fexample.org%3A4321' .
                '&tr=http%3A%2F%2Fexample.org',
            $torrent->getMagnetLink()
        );
    }
}
