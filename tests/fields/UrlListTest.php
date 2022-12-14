<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\Fields;

use Arokettu\Bencode\Bencode;
use PHPUnit\Framework\TestCase;
use SandFox\Torrent\DataTypes\UriList;
use SandFox\Torrent\TorrentFile;

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
        self::assertEquals($uriList->toArray(), $torrent->getRawData()['url-list']);

        // set null
        $torrent->setUrlList(null);
        self::assertArrayNotHasKey('url-list', $torrent->getRawData());

        // set by array
        $torrent->setUrlList($uriList->toArray());
        self::assertEquals($uriList, $torrent->getUrlList());
        self::assertEquals($uriList->toArray(), $torrent->getRawData()['url-list']);
    }

    public function testParse(): void
    {
        // parse null
        $torrent = TorrentFile::loadFromString('de');
        self::assertEquals([], $torrent->getUrlList()->toArray());

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
}
