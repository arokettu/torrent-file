<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Fields;

use Arokettu\Bencode\Bencode;
use Arokettu\Torrent\DataTypes\Node;
use Arokettu\Torrent\DataTypes\NodeList;
use Arokettu\Torrent\TorrentFile;
use PHPUnit\Framework\TestCase;

use function SandFox\Torrent\Tests\raw_torrent_data;

class NodesTest extends TestCase
{
    public function testSetByObject(): void
    {
        $torrent = TorrentFile::loadFromString('de');

        $torrent->setNodes(new NodeList([['localhost', 8080], new Node('127.0.0.1', 10101)]));

        self::assertEquals([
            ['localhost', 8080],
            ['127.0.0.1', 10101],
        ], raw_torrent_data($torrent)['nodes']);
    }

    public function testSetByArray(): void
    {
        $torrent = TorrentFile::loadFromString('de');

        $torrent->setNodes([['localhost', 8080], new Node('127.0.0.1', 10101)]);

        self::assertEquals([
            ['localhost', 8080],
            ['127.0.0.1', 10101],
        ], raw_torrent_data($torrent)['nodes']);
    }

    public function testDuplicate(): void
    {
        $torrent = TorrentFile::loadFromString('de');

        $torrent->setNodes([
            ['localhost', 8080],
            new Node('localhost', 8080),
            ['127.0.0.1', 10101],
            new Node('127.0.0.1', 10101),
        ]);

        self::assertEquals([
            ['localhost', 8080],
            ['127.0.0.1', 10101],
        ], raw_torrent_data($torrent)['nodes']);
    }

    public function testParse(): void
    {
        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'nodes' => [['localhost', 8080], ['127.0.0.1', 10101]],
        ]));

        $nodes = $torrent->getNodes();

        self::assertInstanceOf(NodeList::class, $nodes);
        self::assertInstanceOf(Node::class, $nodes[0]);
        self::assertEquals(2, \count($nodes));
        self::assertEquals('localhost', $nodes[0][0]);
        self::assertEquals(10101, $nodes[1][1]);
    }

    public function testSetEmpty(): void
    {
        $torrent = TorrentFile::loadFromString(Bencode::encode([
            'nodes' => [['localhost', 8080], ['127.0.0.1', 10101]],
        ]));

        $torrent->setNodes([]);

        self::assertArrayNotHasKey('nodes', $torrent->getRawData());
    }
}
