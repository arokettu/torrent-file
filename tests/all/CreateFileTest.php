<?php

namespace SandFox\Torrent\Tests\All;

use PHPUnit\Framework\TestCase;
use SandFox\Torrent\TorrentFile;

use const SandFox\Torrent\Tests\TEST_ROOT;

class CreateFileTest extends TestCase
{
    public function testSingleFile(): void
    {
        $torrentFile = TorrentFile::fromPath(TEST_ROOT . '/data/files/file1.txt'); // approx 6 mb

        $this->assertEquals('6ca8fecb8d4c43183307179652fd31a50f99a912', $torrentFile->getInfoHash());
        $this->assertEquals(260, strlen($torrentFile->getRawData()['info']['pieces'])); // 13 chunks
    }

    public function testMultipleFiles(): void
    {
        $torrentFile = TorrentFile::fromPath(TEST_ROOT . '/data/files'); // approx 19 mb

        $this->assertEquals('2efb80c60b42b261d79b777477276e0b18b47081', $torrentFile->getInfoHash());
        $this->assertEquals(760, strlen($torrentFile->getRawData()['info']['pieces'])); // 38 chunks
    }

    public function testMultipleFiles1MB(): void
    {
        $torrentFile = TorrentFile::fromPath(TEST_ROOT . '/data/files', [
            'pieceLength' => 1024 * 1024, // 1mb chunk
        ]); // approx 19 mb

        $this->assertEquals('5af63cdfb9cdcc9a09bde3fa4b7a9266d8528b7a', $torrentFile->getInfoHash());
        $this->assertEquals(380, strlen($torrentFile->getRawData()['info']['pieces'])); // 19 chunks
    }
}
