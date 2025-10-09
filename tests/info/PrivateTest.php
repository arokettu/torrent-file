<?php

/**
 * @copyright 2017 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Info;

use Arokettu\Bencode\Bencode;
use Arokettu\Torrent\TorrentFile;
use PHPUnit\Framework\TestCase;

final class PrivateTest extends TestCase
{
    public function testSetPrivate(): void
    {
        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['pieces' => '', 'meta version' => 2],
        ]));

        $hash1 = $torrent->v1()->getInfoHash();
        $hash2 = $torrent->v2()->getInfoHash();

        $torrent->setPrivate(true);

        // changing private value must change info hash
        self::assertNotEquals($hash1, $torrent->v1()->getInfoHash());
        self::assertNotEquals($hash2, $torrent->v2()->getInfoHash());

        self::assertEquals(1, $torrent->getRawData()['info']['private']);
    }

    public function testUnsetPrivate(): void
    {
        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'info' => ['private' => 1, 'pieces' => '', 'meta version' => 2],
        ]));

        $hash1 = $torrent->v1()->getInfoHash();
        $hash2 = $torrent->v2()->getInfoHash();

        $torrent->setPrivate(false);

        // changing private value must change info hash
        self::assertNotEquals($hash1, $torrent->v1()->getInfoHash());
        self::assertNotEquals($hash2, $torrent->v2()->getInfoHash());

        self::assertNull($torrent->getRawData()['info']['private'] ?? null);
    }
}
