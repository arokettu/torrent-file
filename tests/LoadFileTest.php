<?php

use PHPUnit\Framework\TestCase;
use SandFoxMe\Torrent\TorrentFile;

class LoadFileTest extends TestCase
{
    public function testLoadFields()
    {
        $torrent = TorrentFile::load(__DIR__ . '/data/CentOS-7-x86_64-NetInstall-1611.torrent');

        $this->assertEquals('http://torrent.centos.org:6969/announce', $torrent->getAnnounce());
        $this->assertEquals([
            ['http://torrent.centos.org:6969/announce'],
            ['http://ipv6.torrent.centos.org:6969/announce'],
        ], $torrent->getAnnounceList());
        $this->assertEquals('CentOS x86_64 NetInstall ISO', $torrent->getComment());
        $this->assertEquals('mktorrent 1.0', $torrent->getCreatedBy());
        $this->assertEquals(1481207147, $torrent->getCreationDate());
        $this->assertFalse($torrent->isPrivate());
        $this->assertEquals('5ff001f9f7501a7e4eee21465ffacda4', md5($torrent->getInfoHash()));
    }
}
