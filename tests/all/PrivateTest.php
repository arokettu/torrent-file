<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\All;

use PHPUnit\Framework\TestCase;
use SandFox\Bencode\Bencode;
use SandFox\Torrent\TorrentFile;

class PrivateTest extends TestCase
{
    public function testSetPrivate(): void
    {
        $torrent = TorrentFile::loadFromString('de');

        $sha = $torrent->getInfoHash();

        $torrent->setPrivate(true);

        self::assertNotEquals($sha, $torrent->getInfoHash()); // changing private value must change info hash

        self::assertEquals(1, $torrent->getRawData()['info']['private']);
    }

    public function testUnsetPrivate(): void
    {
        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['private' => 1],
        ]));

        $sha = $torrent->getInfoHash();

        $torrent->setPrivate(false);

        self::assertNotEquals($sha, $torrent->getInfoHash()); // changing private value must change info hash

        self::assertNull($torrent->getRawData()['info']['private'] ?? null);
    }
}
