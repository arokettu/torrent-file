<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Info;

use Arokettu\Bencode\Bencode;
use Arokettu\Torrent\TorrentFile;
use PHPUnit\Framework\TestCase;

class InfoHashTest extends TestCase
{
    public function testInfoHashes(): void
    {
        // v1

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['pieces' => '']
        ]));
        self::assertEquals(
            [1 => 'd38308ebeda8a85e730b9393f0bb37970c57e78f'],
            $torrent->getInfoHashes(),
        );
        self::assertEquals(
            'd38308ebeda8a85e730b9393f0bb37970c57e78f',
            $torrent->v1()->getInfoHash(),
        );

        // v2

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['meta version' => 2]
        ]));
        self::assertEquals(
            [2 => '11f789319884160645bb421bfdfca60fac20c932cacea32c7757dd300a3765fd'],
            $torrent->getInfoHashes()
        );
        self::assertEquals(
            '11f789319884160645bb421bfdfca60fac20c932cacea32c7757dd300a3765fd',
            $torrent->v2()->getInfoHash(),
        );

        // v1 + v2

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['meta version' => 2, 'pieces' => '']
        ]));
        self::assertEquals([
            1 => '810c7a83166568622e1712c14410243cd836d31c',
            2 => '4bbc2b7563b4ec457114b60a477c5d5775a0f80de5ed6fe3173067ca1109f604',
        ], $torrent->getInfoHashes());

        // unknown

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => []
        ]));
        self::assertEquals([], $torrent->getInfoHashes());
        // getting info hash on broken torrent generates type error, I won't test for that
    }
}
