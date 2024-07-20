<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Files;

use Arokettu\Torrent\TorrentFile;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

use const Arokettu\Torrent\Tests\TEST_ROOT;

class CreateFileEdgeCasesTest extends TestCase
{
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

    public function testUnsetCreationDate(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/small.txt',
            creationDate: null,
        );

        self::assertNull($torrent->getCreationDate());
    }

    public function testDefaultCreatedBy(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/small.txt',
        );

        self::assertEquals(TorrentFile::CREATED_BY, $torrent->getCreatedBy());
    }

    public function testOverrideCreatedBy(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/small.txt',
            createdBy: 'me'
        );

        self::assertEquals('me', $torrent->getCreatedBy());
    }

    public function testUnsetCreatedBy(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/small.txt',
            createdBy: null,
        );

        self::assertNull($torrent->getCreatedBy());
    }
}
