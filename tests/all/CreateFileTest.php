<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\All;

use PHPUnit\Framework\TestCase;
use SandFox\Torrent\Exception\PathNotFoundException;
use SandFox\Torrent\TorrentFile;

use const SandFox\Torrent\Tests\TEST_ROOT;

class CreateFileTest extends TestCase
{
    public function testSingleFile(): void
    {
        $torrent = TorrentFile::fromPath(TEST_ROOT . '/data/files/file1.txt', [
            'md5sum' => true,
        ]); // approx 6 mb

        $this->assertEquals('6092cfe0e10d639229cdb76a1375af37e45c0df7', $torrent->getInfoHash());
        $this->assertEquals(260, strlen($torrent->getRawData()['info']['pieces'])); // 13 chunks
        $this->assertEquals('file1.txt', $torrent->getDisplayName());
        $this->assertEquals('file1.txt.torrent', $torrent->getFileName());

        $this->assertEquals(
            'magnet:?dn=file1.txt&xt=urn:btih:6092CFE0E10D639229CDB76A1375AF37E45C0DF7',
            $torrent->getMagnetLink()
        );
    }

    public function testMultipleFiles(): void
    {
        $torrent = TorrentFile::fromPath(TEST_ROOT . '/data/files', [
            'md5sum' => true,
        ]); // approx 19 mb

        $this->assertEquals('0c8af23beb533d29fe210137439e6c1fce8acaba', $torrent->getInfoHash());
        $this->assertEquals(760, strlen($torrent->getRawData()['info']['pieces'])); // 38 chunks
        $this->assertEquals('files', $torrent->getDisplayName());
        $this->assertEquals('files.torrent', $torrent->getFileName());

        $this->assertEquals(
            'magnet:?dn=files&xt=urn:btih:0C8AF23BEB533D29FE210137439E6C1FCE8ACABA',
            $torrent->getMagnetLink()
        );
    }

    public function testMultipleFiles1MB(): void
    {
        $torrent = TorrentFile::fromPath(TEST_ROOT . '/data/files', [
            'md5sum' => true,
            'pieceLength' => 1024 * 1024, // 1mb chunk
        ]); // approx 19 mb

        $this->assertEquals('7f71b004d89e823b7800e9f27c893c3a97562cea', $torrent->getInfoHash());
        $this->assertEquals(380, strlen($torrent->getRawData()['info']['pieces'])); // 19 chunks
        $this->assertEquals('files', $torrent->getDisplayName());
        $this->assertEquals('files.torrent', $torrent->getFileName());

        $this->assertEquals(
            'magnet:?dn=files&xt=urn:btih:7F71B004D89E823B7800E9F27C893C3A97562CEA',
            $torrent->getMagnetLink()
        );
    }

    public function testNotFoundException(): void
    {
        $this->expectException(PathNotFoundException::class);
        TorrentFile::fromPath(TEST_ROOT . '/data/files/nosuchfile.txt');
    }
}
