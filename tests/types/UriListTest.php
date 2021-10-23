<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\Types;

use PHPUnit\Framework\TestCase;
use SandFox\Torrent\DataTypes\UriList;
use SandFox\Torrent\Exception\BadMethodCallException;
use SandFox\Torrent\Exception\InvalidArgumentException;
use SandFox\Torrent\Exception\OutOfBoundsException;

class UriListTest extends TestCase
{
    private function getUriList(): UriList
    {
        return UriList::create(
            'https://example.com/seed',
            'https://example.org/seed',
        );
    }

    // array getters

    public function testInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new UriList([1, 2, 3]);
    }

    public function testToArray(): void
    {
        $uriList = $this->getUriList();

        self::assertEquals([
            'https://example.com/seed',
            'https://example.org/seed',
        ], $uriList->toArray());
    }

    // modifiers

    public function testAppend(): void
    {
        $uriList = $this->getUriList();

        $uriList = UriList::append($uriList, 'https://example.net/seed');

        self::assertEquals([
            'https://example.com/seed',
            'https://example.org/seed',
            'https://example.net/seed',
        ], $uriList->toArray());

        // no duplicates

        $uriList = UriList::append($uriList, 'https://example.org/seed');

        self::assertEquals([
            'https://example.com/seed',
            'https://example.org/seed',
            'https://example.net/seed',
        ], $uriList->toArray());
    }

    public function testPrepend(): void
    {
        $uriList = $this->getUriList();

        $uriList = UriList::prepend($uriList, 'https://example.net/seed');

        self::assertEquals([
            'https://example.net/seed',
            'https://example.com/seed',
            'https://example.org/seed',
        ], $uriList->toArray());

        // no duplicates but move them forward

        $uriList = UriList::prepend($uriList, 'https://example.org/seed');

        self::assertEquals([
            'https://example.org/seed',
            'https://example.net/seed',
            'https://example.com/seed',
        ], $uriList->toArray());
    }

    public function testRemove(): void
    {
        $uriList = $this->getUriList();

        $uriList = UriList::remove($uriList, 'https://example.org/seed');

        self::assertEquals([
            'https://example.com/seed',
        ], $uriList->toArray());
    }

    // array access

    public function testExists(): void
    {
        $uriList = $this->getUriList();

        self::assertTrue(isset($uriList[0]));
        self::assertTrue(isset($uriList[1]));
        self::assertFalse(isset($uriList[5]));
        self::assertFalse(isset($uriList['key']));
    }

    public function testGet(): void
    {
        $uriList = $this->getUriList();

        self::assertEquals('https://example.com/seed', $uriList[0]);
    }

    public function testGetOOB(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->getUriList()[5];
    }

    public function testImmutableSet(): void
    {
        $this->expectException(BadMethodCallException::class);
        $uriList = $this->getUriList();
        $uriList[0] = '127.0.0.1';
    }

    public function testImmutableUnset(): void
    {
        $this->expectException(BadMethodCallException::class);
        $uriList = $this->getUriList();
        unset($uriList[0]);
    }

    // countable

    public function testCountable(): void
    {
        self::assertEquals(2, \count($this->getUriList()));
    }
}
