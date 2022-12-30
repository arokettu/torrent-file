<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Fields;

use Arokettu\Torrent\DataTypes\AnnounceList;
use Arokettu\Torrent\TorrentFile;
use PHPUnit\Framework\TestCase;

class AnnounceListTest extends TestCase
{
    public function testEmpty(): void
    {
        $torrent = TorrentFile::loadFromString('de');

        // no warning if not set
        self::assertEquals([], $torrent->getAnnounceList()->toArray());

        // allow unset
        $torrent->setAnnounceList(['http://localhost']);
        $torrent->setAnnounceList([]);
        self::assertEquals([], $torrent->getAnnounceList()->toArray());

        // setting empty groups is empty set
        $torrent->setAnnounceList([[], [], [], []]);
        self::assertEquals([], $torrent->getAnnounceList()->toArray());
        self::assertArrayNotHasKey('announce-list', $torrent->getRawData());

        // setting empty groups is empty set
        $torrent->setAnnounceList(new AnnounceList());
        self::assertEquals([], $torrent->getAnnounceList()->toArray());
        self::assertArrayNotHasKey('announce-list', $torrent->getRawData());
    }

    public function testPlainList(): void
    {
        $torrent = TorrentFile::loadFromString('de');

        $torrent->setAnnounceList(['https://example.com/tracker', 'https://example.org/tracker']);

        // trackers should form groups
        self::assertEquals([
            ['https://example.com/tracker'],
            ['https://example.org/tracker'],
        ], $torrent->getAnnounceList()->toArray());
    }

    public function testGroupList(): void
    {
        $torrent = TorrentFile::loadFromString('de');

        $torrent->setAnnounceList([['https://example.com/tracker', 'https://example.org/tracker']]);

        self::assertEquals([
            ['https://example.com/tracker', 'https://example.org/tracker']
        ], $torrent->getAnnounceList()->toArray());
    }

    public function testMixedGrouping(): void
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
        ], $torrent->getAnnounceList()->toArray());
    }

    public function testInvalidNesting(): void
    {
        $torrent = TorrentFile::loadFromString('de');

        $this->expectException(\TypeError::class);
        $torrent->setAnnounceList([[['http://localhost']]]);
    }

    public function testInvalidTypeOn1stLvl(): void
    {
        $torrent = TorrentFile::loadFromString('de');

        $this->expectException(\TypeError::class);
        $torrent->setAnnounceList([123]);
    }

    public function testInvalidTypeOn2ndLvl(): void
    {
        $torrent = TorrentFile::loadFromString('de');

        $this->expectException(\TypeError::class);
        $torrent->setAnnounceList([[123]]);
    }

    public function testUnique(): void
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
            [
                'http://example.biz/announce',
                'http://example.biz/announce',
                'udp://example.biz/announce', // check key reindexing on both levels
            ],
        ]);
        self::assertEquals([
            ['http://example.com/announce'],
            ['http://example.org/announce', 'udp://example.org/announce'],
            ['http://example.net/announce', 'udp://example.net/announce'],
            ['http://example.biz/announce', 'udp://example.biz/announce'],
        ], $torrent->getAnnounceList()->toArray());
    }
}
