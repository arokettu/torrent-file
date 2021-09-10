<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\All;

use PHPUnit\Framework\TestCase;
use SandFox\Torrent\TorrentFile;

class SimpleFieldsTest extends TestCase
{
    public function testComment()
    {
        $torrent = TorrentFile::loadFromString('de');

        // no warning if not set
        self::assertEquals(null, $torrent->getComment());

        // check set
        $torrent->setComment('I am comment');
        self::assertEquals('I am comment', $torrent->getComment());

        // check unset
        $torrent->setComment(null);
        self::assertEquals(null, $torrent->getComment());
    }
}
