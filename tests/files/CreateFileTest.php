<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\Files;

use PHPUnit\Framework\TestCase;
use SandFox\Torrent\Exception\InvalidArgumentException;
use SandFox\Torrent\Exception\PathNotFoundException;
use SandFox\Torrent\TorrentFile;

use function SandFox\Torrent\Tests\build_magnet_link;

use const SandFox\Torrent\Tests\TEST_ROOT;

class CreateFileTest extends TestCase
{
    public function testSingleFile(): void
    {
        $torrent = TorrentFile::fromPath(TEST_ROOT . '/data/files/file1.txt'); // approx 6 mb

        self::assertEquals('3ab5a1739bd320333898510a6cec900a5e6acb7d', $torrent->getInfoHash());
        self::assertEquals(260, \strlen($torrent->getRawData()['info']['pieces'])); // 13 chunks
        self::assertEquals('file1.txt', $torrent->getDisplayName());
        self::assertEquals('file1.txt.torrent', $torrent->getFileName());
        self::assertFalse($torrent->isDirectory());

        self::assertEquals(
            build_magnet_link([
                'xt=urn:btih:3ab5a1739bd320333898510a6cec900a5e6acb7d',
                'dn=file1.txt',
            ]),
            $torrent->getMagnetLink()
        );
    }

    public function testMultipleFiles(): void
    {
        $torrent = TorrentFile::fromPath(TEST_ROOT . '/data/files'); // approx 19 mb

        self::assertEquals('e3bfb18c606631c472b7ba1813bc96c7f748b098', $torrent->getInfoHash());
        self::assertEquals(760, \strlen($torrent->getRawData()['info']['pieces'])); // 38 chunks
        self::assertEquals('files', $torrent->getDisplayName());
        self::assertEquals('files.torrent', $torrent->getFileName());
        self::assertTrue($torrent->isDirectory());

        self::assertEquals(
            build_magnet_link([
                'xt=urn:btih:e3bfb18c606631c472b7ba1813bc96c7f748b098',
                'dn=files',
            ]),
            $torrent->getMagnetLink()
        );
    }

    public function testMultipleFiles1MB(): void
    {
        $torrent = TorrentFile::fromPath(TEST_ROOT . '/data/files', [
            'pieceLength' => 1024 * 1024, // 1mb chunk
        ]); // approx 19 mb

        self::assertEquals('8d7b1593175abfa6563f7c8de082e5c46b3d1292', $torrent->getInfoHash());
        self::assertEquals(380, \strlen($torrent->getRawData()['info']['pieces'])); // 19 chunks
        self::assertEquals('files', $torrent->getDisplayName());
        self::assertEquals('files.torrent', $torrent->getFileName());

        self::assertEquals(
            build_magnet_link([
                'xt=urn:btih:8d7b1593175abfa6563f7c8de082e5c46b3d1292',
                'dn=files',
            ]),
            $torrent->getMagnetLink()
        );
    }

    public function testNotFoundException(): void
    {
        $this->expectException(PathNotFoundException::class);
        TorrentFile::fromPath(TEST_ROOT . '/data/files/nosuchfile.txt');
    }

    public function testChunkTooLow(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('pieceLength must be a power of 2 and at least 16384');

        TorrentFile::fromPath(TEST_ROOT . '/data/files/file1.txt', [
            'pieceLength' => 1024,
        ]);
    }

    public function testChunkNotPow2(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('pieceLength must be a power of 2 and at least 16384');

        TorrentFile::fromPath(TEST_ROOT . '/data/files/file1.txt', [
            'pieceLength' => 1024 * 1024 - 1,
        ]);
    }
}
