<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\All;

use PHPUnit\Framework\TestCase;
use SandFox\Torrent\TorrentFile;

use const SandFox\Torrent\Tests\TEST_ROOT;

class LoadFileTest extends TestCase
{
    public function testLoadFields(): void
    {
        $torrent = TorrentFile::load(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent');

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
            'magnet:?dn=CentOS-7-x86_64-NetInstall-1611&xt=urn:btih:54259D2FAFB1DE5B794E449777748EBA36236F8C' .
                '&tr=http://torrent.centos.org:6969/announce' .
                '&tr=http://ipv6.torrent.centos.org:6969/announce',
            $torrent->getMagnetLink()
        );

        // reencoded file should be exactly same

        $tmpfile = tmpfile();
        fwrite($tmpfile, $torrent->storeToString());
        fflush($tmpfile);

        $this->assertFileEquals(
            TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent',
            stream_get_meta_data($tmpfile)['uri']
        );
    }

    public function testStore(): void
    {
        $torrent = TorrentFile::load(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent');

        $tmpName = tempnam('/tmp', 'tf-test');

        $torrent->store($tmpName);

        $this->assertFileEquals(TEST_ROOT . '/data/CentOS-7-x86_64-NetInstall-1611.torrent', $tmpName);

        unlink($tmpName);
    }
}
