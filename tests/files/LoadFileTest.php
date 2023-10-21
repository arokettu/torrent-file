<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Files;

use Arokettu\Torrent\TorrentFile;
use PHPUnit\Framework\TestCase;

use function Arokettu\Torrent\Tests\build_magnet_link;

use const Arokettu\Torrent\Tests\TEST_ROOT;

class LoadFileTest extends TestCase
{
    public function testLoadFields(): void
    {
        $torrent = TorrentFile::load(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent');

        self::assertEquals('http://torrent.centos.org:6969/announce', $torrent->getAnnounce());
        self::assertEquals([
            ['http://torrent.centos.org:6969/announce'],
            ['http://ipv6.torrent.centos.org:6969/announce'],
        ], $torrent->getAnnounceList()->toArray());
        self::assertEquals('CentOS x86_64 NetInstall ISO', $torrent->getComment());
        self::assertEquals('mktorrent 1.0', $torrent->getCreatedBy());
        self::assertEquals(1481207147, $torrent->getCreationDate()->getTimestamp());
        self::assertFalse($torrent->isPrivate());
        self::assertEquals('54259d2fafb1de5b794e449777748eba36236f8c', $torrent->v1()->getInfoHash());
        self::assertEquals('CentOS-7-x86_64-NetInstall-1611', $torrent->getDisplayName());
        self::assertEquals('CentOS-7-x86_64-NetInstall-1611.torrent', $torrent->getFileName());

        // magnet link

        self::assertEquals(
            build_magnet_link([
                'xt=urn:btih:54259d2fafb1de5b794e449777748eba36236f8c',
                'dn=CentOS-7-x86_64-NetInstall-1611',
                // tr=http://torrent.centos.org:6969/announce
                'tr=http%3A%2F%2Ftorrent.centos.org%3A6969%2Fannounce',
                // tr=http://ipv6.torrent.centos.org:6969/announce
                'tr=http%3A%2F%2Fipv6.torrent.centos.org%3A6969%2Fannounce',
            ]),
            $torrent->getMagnetLink()
        );

        // reencoded file should be exactly same

        $tmpfile = tmpfile();
        fwrite($tmpfile, $torrent->storeToString());
        fflush($tmpfile);

        self::assertFileEquals(
            TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent',
            stream_get_meta_data($tmpfile)['uri']
        );
    }

    public function testStore(): void
    {
        $torrent = TorrentFile::load(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent');

        $tmpName = tempnam('/tmp', 'tf-test');

        $torrent->store($tmpName);

        self::assertFileEquals(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent', $tmpName);

        unlink($tmpName);
    }

    public function testStream(): void
    {
        $torrent = TorrentFile::loadFromStream(
            fopen(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent', 'r')
        );

        $stream = $torrent->storeToStream();
        rewind($stream);

        self::assertEquals(
            file_get_contents(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent'),
            stream_get_contents($stream)
        );
    }
}
