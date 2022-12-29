<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Fields;

use Arokettu\Bencode\Bencode;
use Arokettu\Torrent\TorrentFile;
use PHPUnit\Framework\TestCase;

use function SandFox\Torrent\Tests\raw_torrent_data;

class HttpSeedsTest extends TestCase
{
    public function testSetHttpSeeds(): void
    {
        $torrent = TorrentFile::loadFromString('de');

        $torrent->setHttpSeeds([
            'https://example.com/seed',
            'https://example.org/seed',
        ]);

        self::assertEquals([
            'https://example.com/seed',
            'https://example.org/seed',
        ], raw_torrent_data($torrent)['httpseeds']);
    }

    public function testParseHttpSeeds(): void
    {
        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'httpseeds' => [
                'https://example.com/seed',
                'https://example.org/seed',
            ]
        ]));

        self::assertEquals([
            'https://example.com/seed',
            'https://example.org/seed',
        ], $torrent->getHttpSeeds()->toArray());
    }

    public function testSetEmpty(): void
    {
        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'httpseeds' => [
                'https://example.com/seed',
                'https://example.org/seed',
            ]
        ]));

        $torrent->setHttpSeeds(null);

        self::assertArrayNotHasKey('httpseeds', raw_torrent_data($torrent));
    }
}
