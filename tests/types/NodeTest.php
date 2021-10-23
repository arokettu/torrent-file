<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\Types;

use PHPUnit\Framework\TestCase;
use SandFox\Torrent\DataTypes\Node;
use SandFox\Torrent\Exception\BadMethodCallException;
use SandFox\Torrent\Exception\InvalidArgumentException;
use SandFox\Torrent\Exception\OutOfBoundsException;

class NodeTest extends TestCase
{
    public function testNoInvalidTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Node::ensure(new \ArrayObject());
    }

    public function testNoInvalidArrayStructure(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Node::ensure(['localhost', 80, 'testtest']);
    }

    public function testArrayAccess(): void
    {
        $node = Node::ensure(['localhost', 8080]);

        self::assertTrue(isset($node[0]));
        self::assertTrue(isset($node[1]));
        self::assertFalse(isset($node[3]));
        self::assertFalse(isset($node['host']));
        self::assertEquals('localhost', $node[0]);
        self::assertEquals(8080, $node[1]);
    }

    public function testArrayAccessOOB(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $node = Node::ensure(['localhost', 8080]);
        var_dump($node[3]);
    }

    public function testImmutableSet(): void
    {
        $this->expectException(BadMethodCallException::class);
        $node = Node::ensure(['localhost', 8080]);
        $node[0] = '127.0.0.1';
    }

    public function testImmutableUnset(): void
    {
        $this->expectException(BadMethodCallException::class);
        $node = Node::ensure(['localhost', 8080]);
        unset($node[0]);
    }
}
