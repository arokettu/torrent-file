<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\All;

use PHPUnit\Framework\TestCase;
use SandFox\Torrent\TorrentFile;

use const SandFox\Torrent\Tests\TEST_ROOT;

class SerializiationTest extends TestCase
{
    public function testSerialization(): void
    {
        $torrent = TorrentFile::load(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent');
        $serialized = unserialize(serialize($torrent));

        self::assertEquals($torrent->getInfoHash(), $serialized->getInfoHash());
    }
}
