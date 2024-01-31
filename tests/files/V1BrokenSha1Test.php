<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Files;

use Arokettu\Bencode\Bencode;
use Arokettu\Torrent\Exception\RuntimeException;
use Arokettu\Torrent\TorrentFile;
use Arokettu\Torrent\TorrentFile\Common\Attributes;
use Arokettu\Torrent\TorrentFile\V1\File;
use PHPUnit\Framework\TestCase;

class V1BrokenSha1Test extends TestCase
{
    public function testHexSha1SingleFile(): void
    {
        $data = [
            'info' => [
                'name' => 'filefile',
                'length' => 123456,
                'sha1' => 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3',
                'pieces' => '...', // not read here
            ]
        ];

        $torrent = TorrentFile::loadFromString(Bencode::encode($data));

        self::assertEquals([
            new File(
                path: ['filefile'],
                length: 123456,
                attributes: new Attributes(''),
                sha1bin: hex2bin('a94a8fe5ccb19ba61c4c0873d391e987982fbbd3'),
                symlinkPath: null,
            ),
        ], [...$torrent->v1()->getFiles()]);
    }

    public function testHexSha1MultiFile(): void
    {
        $data = [
            'info' => [
                'files' => [
                    [
                        'path' => ['filefile'],
                        'length' => 123456,
                        'sha1' => 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3',
                    ],
                ],
                'pieces' => '...', // not read here
            ]
        ];

        $torrent = TorrentFile::loadFromString(Bencode::encode($data));

        self::assertEquals([
            new File(
                path: ['filefile'],
                length: 123456,
                attributes: new Attributes(''),
                sha1bin: hex2bin('a94a8fe5ccb19ba61c4c0873d391e987982fbbd3'),
                symlinkPath: null,
            ),
        ], [...$torrent->v1()->getFiles()]);
    }

    public function testWrongHexSha1SingleFile(): void
    {
        $data = [
            'info' => [
                'name' => 'filefile',
                'length' => 123456,
                'sha1' => 'abcdefghijklmnopqrstuvwxyz12346578900000',
                'pieces' => '...', // not read here
            ]
        ];

        $torrent = TorrentFile::loadFromString(Bencode::encode($data));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid sha1 hex digits');
        $torrent->v1()->getFiles();
    }

    public function testWrongHexSha1MultiFile(): void
    {
        $data = [
            'info' => [
                'files' => [
                    [
                        'path' => ['filefile'],
                        'length' => 123456,
                        'sha1' => 'abcdefghijklmnopqrstuvwxyz12346578900000',
                    ],
                ],
                'pieces' => '...', // not read here
            ]
        ];

        $torrent = TorrentFile::loadFromString(Bencode::encode($data));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid sha1 hex digits');
        $torrent->v1()->getFiles();
    }

    public function testNotSha1SingleFile(): void
    {
        $data = [
            'info' => [
                'name' => 'filefile',
                'length' => 123456,
                'sha1' => '123',
                'pieces' => '...', // not read here
            ]
        ];

        $torrent = TorrentFile::loadFromString(Bencode::encode($data));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid sha1 field: must be 20 bytes (standard) or 40 hex digits (legacy)');
        $torrent->v1()->getFiles();
    }

    public function testNotSha1MultiFile(): void
    {
        $data = [
            'info' => [
                'files' => [
                    [
                        'path' => ['filefile'],
                        'length' => 123456,
                        'sha1' => '123',
                    ],
                ],
                'pieces' => '...', // not read here
            ]
        ];

        $torrent = TorrentFile::loadFromString(Bencode::encode($data));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid sha1 field: must be 20 bytes (standard) or 40 hex digits (legacy)');
        $torrent->v1()->getFiles();
    }
}
