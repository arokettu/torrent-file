<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\All;

use PHPUnit\Framework\TestCase;
use SandFox\Torrent\Tests as t;
use SandFox\Torrent\TorrentFile;

class LoadFileTest extends TestCase
{
    public function testLoadFields(): void
    {
        $torrent = TorrentFile::load(t\TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent');

        $this->assertEquals('http://torrent.centos.org:6969/announce', $torrent->getAnnounce());
        $this->assertEquals([
            ['http://torrent.centos.org:6969/announce'],
            ['http://ipv6.torrent.centos.org:6969/announce'],
        ], $torrent->getAnnounceList());
        $this->assertEquals('CentOS x86_64 NetInstall ISO', $torrent->getComment());
        $this->assertEquals('mktorrent 1.0', $torrent->getCreatedBy());
        $this->assertEquals(1481207147, $torrent->getCreationDate());
        $this->assertFalse($torrent->isPrivate());
        $this->assertEquals('54259d2fafb1de5b794e449777748eba36236f8c', $torrent->getInfoHash());
        $this->assertEquals('CentOS-7-x86_64-NetInstall-1611', $torrent->getDisplayName());
        $this->assertEquals('CentOS-7-x86_64-NetInstall-1611.torrent', $torrent->getFileName());

        // magnet link

        $this->assertEquals(
            t\build_magnet_link([
                'xt=urn:btih:54259D2FAFB1DE5B794E449777748EBA36236F8C',
                'dn=CentOS-7-x86_64-NetInstall-1611',
                'tr=http://torrent.centos.org:6969/announce',
                'tr=http://ipv6.torrent.centos.org:6969/announce',
            ]),
            $torrent->getMagnetLink()
        );

        // reencoded file should be exactly same

        $tmpfile = tmpfile();
        fwrite($tmpfile, $torrent->storeToString());
        fflush($tmpfile);

        $this->assertFileEquals(
            t\TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent',
            stream_get_meta_data($tmpfile)['uri']
        );
    }

    public function testStore(): void
    {
        $torrent = TorrentFile::load(t\TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent');

        $tmpName = tempnam('/tmp', 'tf-test');

        $torrent->store($tmpName);

        $this->assertFileEquals(t\TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent', $tmpName);

        unlink($tmpName);
    }
}
