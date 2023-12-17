<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Info;

use Arokettu\Torrent\Exception\RuntimeException;
use Arokettu\Torrent\TorrentFile;
use PHPUnit\Framework\TestCase;

use const Arokettu\Torrent\Tests\TEST_ROOT;

class ImmutableWhenSignedTest extends TestCase
{
    public function testImmutableWhenSigned(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(
            'Unable to modify infohash fields of a signed torrent. Please remove the signatures first'
        );

        $file = TorrentFile::load(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611-signed.torrent');
        $file->setPrivate(true);
    }

    public function testMutableWhenNotSigned(): void
    {
        $file = TorrentFile::load(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611-signed.torrent');
        $file->removeSignatures();
        $file->setPrivate(true);

        self::assertTrue($file->isPrivate());
    }
}
