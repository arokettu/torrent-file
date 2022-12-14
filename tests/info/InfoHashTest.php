<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\Info;

use Arokettu\Bencode\Bencode;
use PHPUnit\Framework\TestCase;
use SandFox\Torrent\TorrentFile;

class InfoHashTest extends TestCase
{
    public function testInfoHashes(): void
    {
        // v1

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['length' => 0]
        ]));
        self::assertEquals(
            [1 => '26f0b584fa6fea9ccc2c627f8f6df9feb752ed96'],
            $torrent->getInfoHashes(),
        );
        self::assertEquals(
            '26f0b584fa6fea9ccc2c627f8f6df9feb752ed96',
            $torrent->getInfoHash(),
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
            $torrent->getInfoHash(),
        );

        // v1 + v2

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['meta version' => 2, 'length' => 0]
        ]));
        self::assertEquals([
            1 => 'e5c50f1621e46db4b5356e3634ba80a5a4984244',
            2 => '97df733df47fd30c2f0fe280eeff81114d69d2d0b6bb8c1f314a9eb52a5bc033',
        ], $torrent->getInfoHashes());
        self::assertEquals(
            '97df733df47fd30c2f0fe280eeff81114d69d2d0b6bb8c1f314a9eb52a5bc033', // v2 takes precedence
            $torrent->getInfoHash(),
        );

        // unknown

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => []
        ]));
        self::assertEquals([], $torrent->getInfoHashes());
        // getting info hash on broken torrent generates type error, I won't test for that
    }
}
