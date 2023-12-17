<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Sign;

use Arokettu\Torrent\TorrentFile;
use OpenSSLCertificate;
use PHPUnit\Framework\TestCase;

use const Arokettu\Torrent\Tests\TEST_ROOT;

class UpdateUrlTest extends TestCase
{
    public function testSetUpdateUrl(): void
    {
        $file = TorrentFile::load(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent');

        self::assertNull($file->getUpdateUrl());
        self::assertNull($file->getOriginator());

        $cert = openssl_x509_read('file://' . TEST_ROOT . '/data/keys/test1.crt');
        $file->setUpdateUrl('http://localhost/update', $cert);

        self::assertEquals('http://localhost/update', $file->getUpdateUrl());
        self::assertInstanceOf(OpenSSLCertificate::class, $file->getOriginator());

        $file->removeUpdateUrl();

        self::assertNull($file->getUpdateUrl());
        self::assertNull($file->getOriginator());
    }
}
