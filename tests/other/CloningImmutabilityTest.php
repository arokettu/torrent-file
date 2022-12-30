<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Other;

use Arokettu\Bencode\Bencode;
use Arokettu\Torrent\TorrentFile;
use PHPUnit\Framework\TestCase;

class CloningImmutabilityTest extends TestCase
{
    public function testClonedObjectDoesNotAffectTheSource(): void
    {
        $data = Bencode::encode([
            'announce-list' => [['https://123', 'https://456'], [['https://789']]],
            'info' => ['private' => 1],
        ]);

        $torrent = TorrentFile::loadFromString($data);

        // adding a field
        $torrent1 = clone $torrent;
        $torrent1->setUrlList(['https://xyz']);

        self::assertEquals($data, $torrent->storeToString());
        self::assertNotEquals($torrent->storeToString(), $torrent1->storeToString());

        // changing a field
        $torrent2 = clone $torrent;
        $torrent2->setAnnounceList(['https://abc']);

        self::assertEquals($data, $torrent->storeToString());
        self::assertNotEquals($torrent->storeToString(), $torrent2->storeToString());

        // adding an info field
        $torrent3 = clone $torrent;
        $torrent3->setName('Torrent Name');

        self::assertEquals($data, $torrent->storeToString());
        self::assertNotEquals($torrent->storeToString(), $torrent3->storeToString());

        // changing an info field
        $torrent4 = clone $torrent;
        $torrent4->setPrivate(false);

        self::assertEquals($data, $torrent->storeToString());
        self::assertNotEquals($torrent->storeToString(), $torrent4->storeToString());
    }
}
