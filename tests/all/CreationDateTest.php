<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\All;

use PHPUnit\Framework\TestCase;
use SandFox\Bencode\Bencode;
use SandFox\Torrent\TorrentFile;

class CreationDateTest extends TestCase
{
    public function testSetCreationDate(): void
    {
        $timestamp = 1_500_000_000;
        $dateTimeM = new \DateTime('@' . $timestamp);
        $dateTimeI = new \DateTimeImmutable('@' . $timestamp);

        $torrent = TorrentFile::loadFromString('de');

        $torrent->setCreationDate($timestamp);
        self::assertEquals($timestamp, $torrent->getCreationDateAsTimestamp());

        // reset and check
        $torrent->setCreationDate(null);
        self::assertNull($torrent->getCreationDateAsTimestamp());

        $torrent->setCreationDate($dateTimeM);
        self::assertEquals($timestamp, $torrent->getCreationDateAsTimestamp());

        // reset and check
        $torrent->setCreationDate(null);
        self::assertNull($torrent->getCreationDateAsTimestamp());

        $torrent->setCreationDate($dateTimeI);
        self::assertEquals($timestamp, $torrent->getCreationDateAsTimestamp());
    }

    public function testGetCreationDate(): void
    {
        $timestamp = 1_500_000_000;
        $dateTime = (new \DateTimeImmutable())->setTimestamp($timestamp);

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'creation date' => $timestamp,
        ]));

        self::assertEquals($timestamp, $torrent->getCreationDate());
        self::assertEquals($timestamp, $torrent->getCreationDateAsTimestamp());
        self::assertEquals($dateTime, $torrent->getCreationDateAsDateTime());
    }

    public function testGetNullCreationDate(): void
    {
        $torrent = TorrentFile::loadFromString('de');

        self::assertNull($torrent->getCreationDate());
        self::assertNull($torrent->getCreationDateAsTimestamp());
        self::assertNull($torrent->getCreationDateAsDateTime());
    }
}
