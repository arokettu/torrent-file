<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\Info;

use PHPUnit\Framework\TestCase;
use SandFox\Torrent\Exception\InvalidArgumentException;
use SandFox\Torrent\TorrentFile;

class NameTest extends TestCase
{
    public function testNameSet(): void
    {
        $torrent = TorrentFile::loadFromString('de');
        self::assertNull($torrent->getName());

        $infoHash1 = $torrent->getInfoHash();

        $torrent->setName('file1.iso');
        self::assertEquals('file1.iso', $torrent->getName());

        $infoHash2 = $torrent->getInfoHash();

        $torrent->setName('file2.iso');
        self::assertEquals('file2.iso', $torrent->getName());

        $infoHash3 = $torrent->getInfoHash();

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
