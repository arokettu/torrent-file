<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\Files;

use PHPUnit\Framework\TestCase;
use SandFox\Torrent\Exception\PathNotFoundException;
use SandFox\Torrent\TorrentFile;

use function SandFox\Torrent\Tests\build_magnet_link;

use const SandFox\Torrent\Tests\TEST_ROOT;

class CreateFileTest extends TestCase
{
    public function testSingleFile(): void
    {
        $torrent = TorrentFile::fromPath(TEST_ROOT . '/data/files/file1.txt', [
            'md5sum' => true,
        ]); // approx 6 mb

        self::assertEquals('6092cfe0e10d639229cdb76a1375af37e45c0df7', $torrent->getInfoHash());
        self::assertEquals(260, \strlen($torrent->getRawData()['info']['pieces'])); // 13 chunks
        self::assertEquals('file1.txt', $torrent->getDisplayName());
        self::assertEquals('file1.txt.torrent', $torrent->getFileName());
        self::assertFalse($torrent->isDirectory());

        self::assertEquals(
            build_magnet_link([
                'xt=urn:btih:6092cfe0e10d639229cdb76a1375af37e45c0df7',
                'dn=file1.txt',
            ]),
            $torrent->getMagnetLink()
        );
    }

    public function testMultipleFiles(): void
    {
        $torrent = TorrentFile::fromPath(TEST_ROOT . '/data/files', [
            'md5sum' => true,
        ]); // approx 19 mb

        self::assertEquals('0c8af23beb533d29fe210137439e6c1fce8acaba', $torrent->getInfoHash());
        self::assertEquals(760, \strlen($torrent->getRawData()['info']['pieces'])); // 38 chunks
        self::assertEquals('files', $torrent->getDisplayName());
        self::assertEquals('files.torrent', $torrent->getFileName());
        self::assertTrue($torrent->isDirectory());

        self::assertEquals(
            build_magnet_link([
                'xt=urn:btih:0c8af23beb533d29fe210137439e6c1fce8acaba',
                'dn=files',
            ]),
            $torrent->getMagnetLink()
        );
    }

    public function testMultipleFiles1MB(): void
    {
        $torrent = TorrentFile::fromPath(TEST_ROOT . '/data/files', [
            'md5sum' => true,
            'pieceLength' => 1024 * 1024, // 1mb chunk
        ]); // approx 19 mb

        self::assertEquals('7f71b004d89e823b7800e9f27c893c3a97562cea', $torrent->getInfoHash());
        self::assertEquals(380, \strlen($torrent->getRawData()['info']['pieces'])); // 19 chunks
        self::assertEquals('files', $torrent->getDisplayName());
        self::assertEquals('files.torrent', $torrent->getFileName());

        self::assertEquals(
            build_magnet_link([
                'xt=urn:btih:7f71b004d89e823b7800e9f27c893c3a97562cea',
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
}
