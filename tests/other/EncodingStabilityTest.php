<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Other;

use Arokettu\Torrent\TorrentFile;
use PHPUnit\Framework\TestCase;

class EncodingStabilityTest extends TestCase
{
    public function testEmptyDictNotBecomingList(): void
    {
        $encoded = 'd4:infodee';
        $torrent = TorrentFile::loadFromString($encoded);

        self::assertEquals($encoded, $torrent->storeToString());
    }

    public function testEmptyListBeingKept(): void
    {
        $encoded = 'd8:url-listlee';
        $torrent = TorrentFile::loadFromString($encoded);

        self::assertEquals([], $torrent->getUrlList()->toArray());
        self::assertEquals($encoded, $torrent->storeToString());

        // but is removed on set
        $torrent->setUrlList($torrent->getUrlList());

        self::assertEquals([], $torrent->getUrlList()->toArray());
        self::assertEquals('de', $torrent->storeToString());
    }
}
