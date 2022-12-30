<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Info;

use Arokettu\Bencode\Bencode;
use Arokettu\Torrent\TorrentFile;
use PHPUnit\Framework\TestCase;

class PrivateTest extends TestCase
{
    public function testSetPrivate(): void
    {
        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['length' => 0],
        ]));

        $sha = $torrent->getInfoHash();

        $torrent->setPrivate(true);

        self::assertNotEquals($sha, $torrent->getInfoHash()); // changing private value must change info hash

        self::assertEquals(1, $torrent->getRawData()['info']['private']);
    }

    public function testUnsetPrivate(): void
    {
        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['private' => 1, 'length' => 0],
        ]));

        $sha = $torrent->getInfoHash();

        $torrent->setPrivate(false);

        self::assertNotEquals($sha, $torrent->getInfoHash()); // changing private value must change info hash

        self::assertNull($torrent->getRawData()['info']['private'] ?? null);
    }
}
