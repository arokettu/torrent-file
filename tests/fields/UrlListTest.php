<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Fields;

use Arokettu\Bencode\Bencode;
use Arokettu\Torrent\DataTypes\UriList;
use Arokettu\Torrent\TorrentFile;
use PHPUnit\Framework\TestCase;

use function Arokettu\Torrent\Tests\raw_torrent_data;

class UrlListTest extends TestCase
{
    public function testSet(): void
    {
        $torrent = TorrentFile::loadFromString('de');

        $uriList = UriList::create(
            'https://example.com/files/test.iso',
            'https://example.org/files/test.iso',
        );

        // set by object
        $torrent->setUrlList($uriList);
        self::assertEquals($uriList, $torrent->getUrlList());
        self::assertEquals($uriList->toArray(), raw_torrent_data($torrent)['url-list']);

        // set null
        $torrent->setUrlList(null);
        self::assertArrayNotHasKey('url-list', raw_torrent_data($torrent));

        // set by array
        $torrent->setUrlList($uriList->toArray());
        self::assertEquals($uriList, $torrent->getUrlList());
        self::assertEquals($uriList->toArray(), raw_torrent_data($torrent)['url-list']);
    }

    public function testParse(): void
    {
        // parse null
        $torrent = TorrentFile::loadFromString('de');
        self::assertEquals([], $torrent->getUrlList()->toArray());
        self::assertTrue($torrent->getUrlList()->empty());

        // parse string
        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'url-list' => 'https://example.com/files/test.iso',
        ]));
        self::assertEquals([
            'https://example.com/files/test.iso',
        ], $torrent->getUrlList()->toArray());

        // parse array
        $torrent = TorrentFile::loadFromString(Bencode::encode(['url-list' => [
            'https://example.com/files/test.iso',
            'https://example.org/files/test.iso',
        ]]));
        self::assertEquals([
            'https://example.com/files/test.iso',
            'https://example.org/files/test.iso',
        ], $torrent->getUrlList()->toArray());
    }

    public function testAcceptsStringInFile(): void
    {
        $data1 = [
            'url-list' => 'http://localhost',
        ];
        $data2 = [
            'url-list' => ['http://localhost'],
        ];

        $torrent = TorrentFile::loadFromString(Bencode::encode($data1));
        self::assertEquals(['http://localhost'], $torrent->getUrlList()->toArray());

        self::assertEquals(Bencode::encode($data2), $torrent->setUrlList($torrent->getUrlList())->storeToString());
    }
}
