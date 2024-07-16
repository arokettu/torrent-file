<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Files;

use Arokettu\Clock\StaticClock;
use Arokettu\Torrent\TorrentFile;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

use const Arokettu\Torrent\Tests\TEST_ROOT;

class CreateFileEdgeCasesTest extends TestCase
{
    public function testClockStillAccepted(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/small.txt',
            clock: StaticClock::fromTimestamp(1_600_000_000),
        );

        self::assertEquals(new DateTimeImmutable('@' . 1_600_000_000), $torrent->getCreationDate());
    }

    public function testCreationDateTakesPrecedenceToClock(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/small.txt',
            clock: StaticClock::fromTimestamp(1_600_000_000),
            creationDate: new DateTimeImmutable('@' . 1_700_000_000),
        );

        self::assertEquals(new DateTimeImmutable('@' . 1_700_000_000), $torrent->getCreationDate());
    }

    public function testSystemTime(): void
    {
        $t1 = new DateTimeImmutable('@' . time()); // round to seconds
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/small.txt',
        );
        $t2 = new DateTimeImmutable('now');

        self::assertGreaterThanOrEqual($t1, $torrent->getCreationDate());
        self::assertLessThanOrEqual($t2, $torrent->getCreationDate());
    }
}
