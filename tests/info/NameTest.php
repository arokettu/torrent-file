<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Info;

use Arokettu\Bencode\Bencode;
use Arokettu\Torrent\Exception\InvalidArgumentException;
use Arokettu\Torrent\MetaVersion;
use Arokettu\Torrent\TorrentFile;
use PHPUnit\Framework\TestCase;

class NameTest extends TestCase
{
    public function testNameSet(): void
    {
        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['pieces' => ''],
        ]));
        self::assertNull($torrent->getName());

        $infoHash1 = $torrent->getInfoHash(MetaVersion::V1);

        $torrent->setName('file1.iso');
        self::assertEquals('file1.iso', $torrent->getName());

        $infoHash2 = $torrent->getInfoHash(MetaVersion::V1);

        $torrent->setName('file2.iso');
        self::assertEquals('file2.iso', $torrent->getName());

        $infoHash3 = $torrent->getInfoHash(MetaVersion::V1);

        self::assertNotEquals($infoHash1, $infoHash2);
        self::assertNotEquals($infoHash1, $infoHash3);
        self::assertNotEquals($infoHash2, $infoHash3);
    }

    public function testNotEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $torrent = TorrentFile::loadFromString('de');
        $torrent->setName('');
    }

    public function testNoZeros(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $torrent = TorrentFile::loadFromString('de');
        $torrent->setName("Test\0");
    }

    public function testNoSlashes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $torrent = TorrentFile::loadFromString('de');
        $torrent->setName('te/st');
    }
}
