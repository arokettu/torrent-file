<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Sign;

use Arokettu\Torrent\DataTypes\Signature;
use Arokettu\Torrent\DataTypes\SignatureValidatorResult;
use Arokettu\Torrent\Exception\RuntimeException;
use Arokettu\Torrent\TorrentFile;
use OpenSSLCertificate;
use PHPUnit\Framework\TestCase;

use const Arokettu\Torrent\Tests\TEST_ROOT;

class SignatureTest extends TestCase
{
    public function testListSignatures(): void
    {
        // no signatures
        $file1 = TorrentFile::load(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent');
        $signatures = $file1->getSignatures();
        self::assertEquals([], $signatures->toArray());

        $file2 = TorrentFile::load(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611-signed.torrent');
        /** @var Signature[] $signatures */
        $signatures = $file2->getSignatures();

        self::assertEquals(
            'b0b966cbd7e3293a423442f9b1e7844809e0fb7e',
            sha1($signatures['com.example']->signature)
        );
        self::assertEquals(
            '1034de3e2b8841f6c09aff2356f49b6bbdea0866',
            sha1($signatures['com.example']->certificateDer)
        );
        self::assertEquals(
            '513c4132e8ce4f1002a8c13b146e74a695a89032',
            sha1($signatures['com.example']->certificatePem)
        );
        self::assertInstanceOf(OpenSSLCertificate::class, $signatures['com.example']->certificate);
        self::assertEquals([], $signatures['com.example']->info->toArray());

        self::assertEquals(
            'fbd21008b83a2c0eb17abafc48daae71d439ffce',
            sha1($signatures['org.example']->signature)
        );
        self::assertNull($signatures['org.example']->certificateDer);
        self::assertNull($signatures['org.example']->certificatePem);
        self::assertNull($signatures['org.example']->certificate);
        self::assertEquals([], $signatures['org.example']->info->toArray());

        $file2->removeSignatures();
        $signatures = $file2->getSignatures();
        self::assertEquals([], $signatures->toArray());
    }

    public function testVerifySignatures(): void
    {
        $file = TorrentFile::load(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611-signed.torrent');

        $cert1 = openssl_x509_read('file://' . TEST_ROOT . '/data/keys/test1.crt');
        $cert2 = openssl_x509_read('file://' . TEST_ROOT . '/data/keys/test2.crt');
        $cert3 = openssl_x509_read('file://' . TEST_ROOT . '/data/keys/test2-t1cn.crt');

        self::assertEquals(SignatureValidatorResult::Valid, $file->verifySignature($cert1));
        self::assertEquals(SignatureValidatorResult::Valid, $file->verifySignature($cert2));
        self::assertEquals(SignatureValidatorResult::Invalid, $file->verifySignature($cert3));
    }

    public function testVerifySignaturesNoSignature(): void
    {
        $file = TorrentFile::load(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent');

        $cert1 = openssl_x509_read('file://' . TEST_ROOT . '/data/keys/test1.crt');
        self::assertEquals(SignatureValidatorResult::NotPresent, $file->verifySignature($cert1));
    }

    public function testVerifySignaturesMustBeCN(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The certificate must contain a common name');

        $file = TorrentFile::load(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611-signed.torrent');

        $cert1 = openssl_x509_read('file://' . TEST_ROOT . '/data/keys/test1-nocn.crt');
        self::assertEquals(SignatureValidatorResult::NotPresent, $file->verifySignature($cert1));
    }

    public function testSign(): void
    {
        $file = TorrentFile::load(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent');

        $cert = openssl_x509_read('file://' . TEST_ROOT . '/data/keys/test1.crt');
        $key = openssl_pkey_get_private('file://' . TEST_ROOT . '/data/keys/test1.key');

        $file->sign($key, $cert);

        self::assertEquals(
            'b0b966cbd7e3293a423442f9b1e7844809e0fb7e',
            sha1($file->getSignatures()['com.example']->signature)
        );
        self::assertEquals(
            '1034de3e2b8841f6c09aff2356f49b6bbdea0866',
            sha1($file->getSignatures()['com.example']->certificateDer)
        );
    }

    public function testSignNoCert(): void
    {
        $file = TorrentFile::load(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent');

        $cert = openssl_x509_read('file://' . TEST_ROOT . '/data/keys/test1.crt');
        $key = openssl_pkey_get_private('file://' . TEST_ROOT . '/data/keys/test1.key');

        $file->sign($key, $cert, false);

        self::assertEquals(
            'b0b966cbd7e3293a423442f9b1e7844809e0fb7e',
            sha1($file->getSignatures()['com.example']->signature)
        );
        self::assertNull($file->getSignatures()['com.example']->certificateDer);
    }

    public function testSignMustHaveCN(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The certificate must contain a common name');

        $file = TorrentFile::load(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent');

        $cert = openssl_x509_read('file://' . TEST_ROOT . '/data/keys/test1-nocn.crt');
        $key = openssl_pkey_get_private('file://' . TEST_ROOT . '/data/keys/test1.key');

        $file->sign($key, $cert);
    }

    public function testSignCertKeyMatch(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The key does not correspond to the certificate');

        $file = TorrentFile::load(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent');

        $cert = openssl_x509_read('file://' . TEST_ROOT . '/data/keys/test2.crt');
        $key = openssl_pkey_get_private('file://' . TEST_ROOT . '/data/keys/test1.key');

        $file->sign($key, $cert);
    }

    public function testSignWithInfo(): void
    {
        $file = TorrentFile::load(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent');

        $cert = openssl_x509_read('file://' . TEST_ROOT . '/data/keys/test1.crt');
        $key = openssl_pkey_get_private('file://' . TEST_ROOT . '/data/keys/test1.key');

        $file->sign($key, $cert, true, ['test' => 'abc']);

        self::assertEquals(
            '681b39028439eaebb621694c5393fec2a68afd5e',
            sha1($file->getSignatures()['com.example']->signature)
        );
        self::assertEquals(['test' => 'abc'], $file->getSignatures()['com.example']->info->toArray());

        self::assertEquals(SignatureValidatorResult::Valid, $file->verifySignature($cert));
    }

    public function testSignEquals(): void
    {
        $file1 = TorrentFile::load(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent');

        $cert1 = openssl_x509_read('file://' . TEST_ROOT . '/data/keys/test1.crt');
        $key1 = openssl_pkey_get_private('file://' . TEST_ROOT . '/data/keys/test1.key');
        $cert2 = openssl_x509_read('file://' . TEST_ROOT . '/data/keys/test2.crt');
        $key2 = openssl_pkey_get_private('file://' . TEST_ROOT . '/data/keys/test2.key');

        $file1->sign($key1, $cert1);
        $file1->sign($key2, $cert2, false);

        self::assertEquals(
            file_get_contents(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611-signed.torrent'),
            $file1->storeToString()
        );
    }
}
