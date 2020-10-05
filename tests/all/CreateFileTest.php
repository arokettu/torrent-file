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

        $piecesHashes = $torrentFile->getRawData()['info']['pieces'];

        $this->assertEquals('6f714b23b94595c694357083492c363c', md5($piecesHashes));
        $this->assertEquals(260, strlen($piecesHashes)); // 13 chunks
    }

    public function testMultipleFiles(): void
    {
        $torrentFile = TorrentFile::fromPath(TEST_ROOT . '/data/files'); // approx 19 mb

        $piecesHashes = $torrentFile->getRawData()['info']['pieces'];

        $this->assertEquals('9cdfab1d0a4423dc2d2fd5533d374e4f', md5($piecesHashes));
        $this->assertEquals(760, strlen($piecesHashes)); // 38 chunks
    }

    public function testMultipleFiles1MB(): void
    {
        $torrentFile = TorrentFile::fromPath(TEST_ROOT . '/data/files', [
            'pieceLength' => 1024 * 1024, // 1mb chunk
        ]); // approx 19 mb

        $piecesHashes = $torrentFile->getRawData()['info']['pieces'];

        $this->assertEquals('428ff41d4db721e925ef0e022eaa6cdf', md5($piecesHashes));
        $this->assertEquals(380, strlen($piecesHashes)); // 19 chunks
    }
}
