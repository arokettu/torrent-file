<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Info;

use Arokettu\Bencode\Bencode;
use Arokettu\Torrent\MetaVersion;
use Arokettu\Torrent\TorrentFile;
use PHPUnit\Framework\TestCase;

class PrivateTest extends TestCase
{
    public function testSetPrivate(): void
    {
        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['pieces' => '', 'meta version' => 2],
        ]));

        $hash1 = $torrent->getInfoHash(MetaVersion::V1);
        $hash2 = $torrent->getInfoHash(MetaVersion::V2);

        $torrent->setPrivate(true);

        // changing private value must change info hash
        self::assertNotEquals($hash1, $torrent->getInfoHash(MetaVersion::V1));
        self::assertNotEquals($hash2, $torrent->getInfoHash(MetaVersion::V2));

        self::assertEquals(1, $torrent->getRawData()['info']['private']);
    }

    public function testUnsetPrivate(): void
    {
        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['private' => 1, 'pieces' => '', 'meta version' => 2],
        ]));

        $hash1 = $torrent->getInfoHash(MetaVersion::V1);
        $hash2 = $torrent->getInfoHash(MetaVersion::V2);

        $torrent->setPrivate(false);

        // changing private value must change info hash
        self::assertNotEquals($hash1, $torrent->getInfoHash(MetaVersion::V1));
        self::assertNotEquals($hash2, $torrent->getInfoHash(MetaVersion::V2));

        self::assertNull($torrent->getRawData()['info']['private'] ?? null);
    }
}
