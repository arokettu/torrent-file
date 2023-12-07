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
            'magnet:?xt=urn:btih:54259d2fafb1de5b794e449777748eba36236f8c' .
                '&dn=CentOS-7-x86_64-NetInstall-1611' .
                '&tr=http%3A%2F%2Ftorrent.centos.org%3A6969%2Fannounce' .
                '&tr=http%3A%2F%2Fipv6.torrent.centos.org%3A6969%2Fannounce',
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
}
