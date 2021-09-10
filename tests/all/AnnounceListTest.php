<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\All;

use PHPUnit\Framework\TestCase;
use SandFox\Torrent\Exception\InvalidArgumentException;
use SandFox\Torrent\TorrentFile;

class AnnounceListTest extends TestCase
{
    public function testEmpty()
    {
        $torrent = TorrentFile::loadFromString('de');

        // no warning if not set
        self::assertEquals([], $torrent->getAnnounceList());

        // allow unset
        $torrent->setAnnounceList(['http://localhost']);
        $torrent->setAnnounceList([]);
        self::assertEquals([], $torrent->getAnnounceList());

        // setting empty groups is empty set
        $torrent->setAnnounceList([[], [], [], []]);
        self::assertEquals([], $torrent->getAnnounceList());
        self::assertNull($torrent->getRawData()['announce-list'] ?? null);
    }

    public function testPlainList()
    {
        $torrent = TorrentFile::loadFromString('de');

        $torrent->setAnnounceList(['https://example.com/tracker', 'https://example.org/tracker']);

        // trackers should form groups
        self::assertEquals([
            ['https://example.com/tracker'],
            ['https://example.org/tracker'],
        ], $torrent->getAnnounceList());
    }

    public function testGroupList()
    {
        $torrent = TorrentFile::loadFromString('de');

        $torrent->setAnnounceList([['https://example.com/tracker', 'https://example.org/tracker']]);

        self::assertEquals([
            ['https://example.com/tracker', 'https://example.org/tracker']
        ], $torrent->getAnnounceList());
    }

    public function testMixedGrouping()
    {
        $torrent = TorrentFile::loadFromString('de');

        $torrent->setAnnounceList([
            ['https://example.com/tracker', 'https://example.org/tracker'],
            [], // empty group will be unset
            'https://example.net/tracker', // will be converted to a group
            ['https://example.info/tracker'],
        ]);

        self::assertEquals([
            ['https://example.com/tracker', 'https://example.org/tracker'],
            ['https://example.net/tracker'],
            ['https://example.info/tracker'],
        ], $torrent->getAnnounceList());
    }

    public function testInvalidNesting()
    {
        $torrent = TorrentFile::loadFromString('de');

        $this->expectException(InvalidArgumentException::class);
        $torrent->setAnnounceList([[['http://localhost']]]);
    }

    public function testInvalidTypeOn1stLvl()
    {
        $torrent = TorrentFile::loadFromString('de');

        $this->expectException(InvalidArgumentException::class);
        $torrent->setAnnounceList([123]);
    }

    public function testInvalidTypeOn2ndLvl()
    {
        $torrent = TorrentFile::loadFromString('de');

        $this->expectException(InvalidArgumentException::class);
        $torrent->setAnnounceList([[123]]);
    }

    public function testUnique()
    {
        $torrent = TorrentFile::loadFromString('de');

        $torrent->setAnnounceList([
            'http://example.com/announce',
            [
                'http://example.org/announce',
                'udp://example.org/announce',
                'http://example.org/announce', // in-group repeats are filtered
            ],
            ['http://example.com/announce'], // group repeats are filtered
            ['http://example.net/announce', 'udp://example.net/announce'],
            ['http://example.net/announce', 'udp://example.net/announce'], // more complex group test
            ['http://example.org/announce', 'udp://example.org/announce'], // groups are compared after in-group removal
        ]);
        self::assertEquals([
            ['http://example.com/announce'],
            ['http://example.org/announce', 'udp://example.org/announce'],
            ['http://example.net/announce', 'udp://example.net/announce'],
        ], $torrent->getAnnounceList());
    }
}
