<?php

/**
 * @copyright 2017 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Files;

use Arokettu\Torrent\TorrentFile;
use PHPUnit\Framework\TestCase;

use const Arokettu\Torrent\Tests\TEST_ROOT;

final class SerializiationTest extends TestCase
{
    public function testSerialization(): void
    {
        $torrent = TorrentFile::load(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent');
        $serialized = unserialize(serialize($torrent));

        self::assertEquals($torrent->v1()->getInfoHash(), $serialized->v1()->getInfoHash());
    }
}
