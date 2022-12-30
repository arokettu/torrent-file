<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Fields;

use Arokettu\Torrent\TorrentFile;
use PHPUnit\Framework\TestCase;

class SimpleFieldsTest extends TestCase
{
    public function testComment(): void
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
