<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\All;

use PHPUnit\Framework\TestCase;
use SandFox\Torrent\DataTypes\Node;
use SandFox\Torrent\DataTypes\NodeList;
use SandFox\Torrent\Exception\BadMethodCallException;
use SandFox\Torrent\Exception\OutOfBoundsException;

class NodeListModelTest extends TestCase
{
    private function getNodeList(): NodeList
    {
        return NodeList::create(
            ['localhost', 8080],
            ['127.0.0.1', 10101],
        );
    }

    // array getters

    public function testToArray(): void
    {
        $nodeList = $this->getNodeList();

        self::assertEquals([
            ['localhost', 8080],
            ['127.0.0.1', 10101],
        ], $nodeList->toArray());
        self::assertEquals([
            new Node('localhost', 8080),
            new Node('127.0.0.1', 10101),
        ], $nodeList->toArrayOfNodes());
    }

    // modifiers

    public function testAppend(): void
    {
        $nodeList = $this->getNodeList();

        $nodeList = NodeList::append($nodeList, new Node('[::1]', 443), ['test', 80]);

        self::assertEquals([
            ['localhost', 8080],
            ['127.0.0.1', 10101],
            ['[::1]', 443],
            ['test', 80],
        ], $nodeList->toArray());

        // no duplicates

        $nodeList = NodeList::append($nodeList, ['localhost', 8080]);

        self::assertEquals([
            ['localhost', 8080],
            ['127.0.0.1', 10101],
            ['[::1]', 443],
            ['test', 80],
        ], $nodeList->toArray());
    }

    public function testPrepend(): void
    {
        $nodeList = $this->getNodeList();

        $nodeList = NodeList::prepend($nodeList, new Node('[::1]', 443), ['test', 80]);

        self::assertEquals([
            ['[::1]', 443],
            ['test', 80],
            ['localhost', 8080],
            ['127.0.0.1', 10101],
        ], $nodeList->toArray());

        // no duplicates but move them forward

        $nodeList = NodeList::prepend($nodeList, ['127.0.0.1', 10101]);

        self::assertEquals([
            ['127.0.0.1', 10101],
            ['[::1]', 443],
            ['test', 80],
            ['localhost', 8080],
        ], $nodeList->toArray());
    }

    public function testRemove(): void
    {
        $nodeList = $this->getNodeList();

        $nodeList = NodeList::remove($nodeList, ['localhost', 8080]);

        self::assertEquals([
            ['127.0.0.1', 10101],
        ], $nodeList->toArray());
    }

    // array access

    public function testExists(): void
    {
        $nodeList = $this->getNodeList();

        self::assertTrue(isset($nodeList[0]));
        self::assertTrue(isset($nodeList[1]));
        self::assertFalse(isset($nodeList[5]));
        self::assertFalse(isset($nodeList['key']));
    }

    public function testGetOOB(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->getNodeList()[5];
    }

    public function testImmutableSet(): void
    {
        $this->expectException(BadMethodCallException::class);
        $nodeList = $this->getNodeList();
        $nodeList[0] = '127.0.0.1';
    }

    public function testImmutableUnset(): void
    {
        $this->expectException(BadMethodCallException::class);
        $nodeList = $this->getNodeList();
        unset($nodeList[0]);
    }
}
