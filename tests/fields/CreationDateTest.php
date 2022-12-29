<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Fields;

use Arokettu\Bencode\Bencode;
use Arokettu\Torrent\TorrentFile;
use PHPUnit\Framework\TestCase;

class CreationDateTest extends TestCase
{
    public function testSetCreationDate(): void
    {
        $timestamp = 1_500_000_000;
        $dateTimeM = new \DateTime('@' . $timestamp);
        $dateTimeI = new \DateTimeImmutable('@' . $timestamp);

        $torrent = TorrentFile::loadFromString('de');

        $torrent->setCreationDate($timestamp);
        self::assertEquals($timestamp, $torrent->getCreationDate()?->getTimestamp());

        // reset and check
        $torrent->setCreationDate(null);
        self::assertNull($torrent->getCreationDate()?->getTimestamp());

        $torrent->setCreationDate($dateTimeM);
        self::assertEquals($timestamp, $torrent->getCreationDate()?->getTimestamp());

        // reset and check
        $torrent->setCreationDate(null);
        self::assertNull($torrent->getCreationDate()?->getTimestamp());

        $torrent->setCreationDate($dateTimeI);
        self::assertEquals($timestamp, $torrent->getCreationDate()?->getTimestamp());
    }

    public function testGetCreationDate(): void
    {
        $timestamp = 1_500_000_000;
        $dateTime = (new \DateTimeImmutable())->setTimestamp($timestamp);

        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'creation date' => $timestamp,
        ]));

        self::assertEquals($dateTime, $torrent->getCreationDate());
    }

    public function testGetNullCreationDate(): void
    {
        $torrent = TorrentFile::loadFromString('de');

        self::assertNull($torrent->getCreationDate());
    }
}
