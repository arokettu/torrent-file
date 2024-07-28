<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Info;

use Arokettu\Bencode\Bencode;
use Arokettu\Torrent\Exception\UnexpectedValueException;
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

        $infoHash1 = $torrent->v1()->getInfoHash();

        $torrent->setName('file1.iso');
        self::assertEquals('file1.iso', $torrent->getName());

        $infoHash2 = $torrent->v1()->getInfoHash();

        $torrent->setName('file2.iso');
        self::assertEquals('file2.iso', $torrent->getName());

        $infoHash3 = $torrent->v1()->getInfoHash();

        self::assertNotEquals($infoHash1, $infoHash2);
        self::assertNotEquals($infoHash1, $infoHash3);
        self::assertNotEquals($infoHash2, $infoHash3);
    }

    public function testNotEmpty(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $torrent = TorrentFile::loadFromString('de');
        $torrent->setName('');
    }

    public function testNoZeros(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $torrent = TorrentFile::loadFromString('de');
        $torrent->setName("Test\0");
    }

    public function testNoSlashes(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $torrent = TorrentFile::loadFromString('de');
        $torrent->setName('te/st');
    }
}
