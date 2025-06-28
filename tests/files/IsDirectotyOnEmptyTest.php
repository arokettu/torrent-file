<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Files;

use Arokettu\Bencode\Bencode;
use Arokettu\Torrent\TorrentFile;
use PHPUnit\Framework\TestCase;

class IsDirectotyOnEmptyTest extends TestCase
{
    public function testV1(): void
    {
        $data = [
            'info' => [
                'files' => [],
                'name' => 'torrent name',
                'piece length' => 262144,
                'pieces' => '',
                'private' => 1,
            ],
        ];

        $torrent = TorrentFile::loadFromString(Bencode::encode($data));

        self::assertTrue($torrent->v1()->isDirectory());
    }

    public function testV2(): void
    {
        $data = [
            'info' => [
                'file tree' => [],
                'name' => 'torrent name',
                'meta version' => 2,
                'piece length' => 262144,
                'private' => 1,
            ],
        ];

        $torrent = TorrentFile::loadFromString(Bencode::encode($data));

        self::assertTrue($torrent->v2()->isDirectory());
    }
}
