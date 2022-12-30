<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Types;

use Arokettu\Torrent\DataTypes\AnnounceList;
use Arokettu\Torrent\DataTypes\UriList;
use Arokettu\Torrent\Exception\BadMethodCallException;
use Arokettu\Torrent\Exception\OutOfBoundsException;
use PHPUnit\Framework\TestCase;

class AnnounceListTest extends TestCase
{
    private function getAnnounceList(): AnnounceList
    {
        return AnnounceList::create(
            ['https://example.com/announce'],
            ['https://example.org/announce', 'udp://example.org/announce'],
            ['https://example.net/announce', 'udp://example.net/announce'],
            ['https://example.biz/announce', 'udp://example.biz/announce'],
        );
    }

    // array getters

    public function testToArray(): void
    {
        $announceList = $this->getAnnounceList();

        self::assertEquals([
            ['https://example.com/announce'],
            ['https://example.org/announce', 'udp://example.org/announce'],
            ['https://example.net/announce', 'udp://example.net/announce'],
            ['https://example.biz/announce', 'udp://example.biz/announce'],
        ], $announceList->toArray());
        self::assertEquals([
            UriList::ensure(['https://example.com/announce']),
            UriList::ensure(['https://example.org/announce', 'udp://example.org/announce']),
            UriList::ensure(['https://example.net/announce', 'udp://example.net/announce']),
            UriList::ensure(['https://example.biz/announce', 'udp://example.biz/announce']),
        ], $announceList->toArrayOfUriLists());
    }

    // modifiers

    public function testAppend(): void
    {
        $announceList = $this->getAnnounceList();

        $announceList = AnnounceList::append(
            $announceList,
            ['https://example.edu/announce', 'udp://example.edu/announce'],
            new UriList(['https://example.int/announce', 'udp://example.int/announce']),
        );

        self::assertEquals([
            ['https://example.com/announce'],
            ['https://example.org/announce', 'udp://example.org/announce'],
            ['https://example.net/announce', 'udp://example.net/announce'],
            ['https://example.biz/announce', 'udp://example.biz/announce'],
            ['https://example.edu/announce', 'udp://example.edu/announce'],
            ['https://example.int/announce', 'udp://example.int/announce'],
        ], $announceList->toArray());

        // no duplicates

        $announceList = AnnounceList::append(
            $announceList,
            ['https://example.net/announce', 'udp://example.net/announce'],
        );

        self::assertEquals([
            ['https://example.com/announce'],
            ['https://example.org/announce', 'udp://example.org/announce'],
            ['https://example.net/announce', 'udp://example.net/announce'],
            ['https://example.biz/announce', 'udp://example.biz/announce'],
            ['https://example.edu/announce', 'udp://example.edu/announce'],
            ['https://example.int/announce', 'udp://example.int/announce'],
        ], $announceList->toArray());
    }

    public function testPrepend(): void
    {
        $announceList = $this->getAnnounceList();

        $announceList = AnnounceList::prepend(
            $announceList,
            ['https://example.edu/announce', 'udp://example.edu/announce'],
            new UriList(['https://example.int/announce', 'udp://example.int/announce']),
        );

        self::assertEquals([
            ['https://example.edu/announce', 'udp://example.edu/announce'],
            ['https://example.int/announce', 'udp://example.int/announce'],
            ['https://example.com/announce'],
            ['https://example.org/announce', 'udp://example.org/announce'],
            ['https://example.net/announce', 'udp://example.net/announce'],
            ['https://example.biz/announce', 'udp://example.biz/announce'],
        ], $announceList->toArray());

        // no duplicates

        $announceList = AnnounceList::prepend(
            $announceList,
            ['https://example.net/announce', 'udp://example.net/announce'],
        );

        self::assertEquals([
            ['https://example.net/announce', 'udp://example.net/announce'],
            ['https://example.edu/announce', 'udp://example.edu/announce'],
            ['https://example.int/announce', 'udp://example.int/announce'],
            ['https://example.com/announce'],
            ['https://example.org/announce', 'udp://example.org/announce'],
            ['https://example.biz/announce', 'udp://example.biz/announce'],
        ], $announceList->toArray());
    }

    public function testRemove(): void
    {
        $announceList = $this->getAnnounceList();

        $announceList = AnnounceList::remove(
            $announceList,
            ['https://example.net/announce', 'udp://example.net/announce'],
        );

        self::assertEquals([
            ['https://example.com/announce'],
            ['https://example.org/announce', 'udp://example.org/announce'],
            ['https://example.biz/announce', 'udp://example.biz/announce'],
        ], $announceList->toArray());
    }

    // countable

    public function testCountable(): void
    {
        self::assertEquals(4, \count($this->getAnnounceList()));
    }

    // array access

    public function testExists(): void
    {
        $announceList = $this->getAnnounceList();

        self::assertTrue(isset($announceList[0]));
        self::assertTrue(isset($announceList[1][1]));
        self::assertFalse(isset($announceList[5]));
        self::assertFalse(isset($announceList['key']));
    }

    public function testGet(): void
    {
        $announceList = $this->getAnnounceList();

        self::assertInstanceOf(UriList::class, $announceList[0]);
        self::assertEquals('udp://example.net/announce', $announceList[2][1]);
    }

    public function testGetOOB(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->getAnnounceList()[5];
    }

    public function testImmutableSet(): void
    {
        $this->expectException(BadMethodCallException::class);
        $announceList = $this->getAnnounceList();
        $announceList[0] = ['http://localhost/announce'];
    }

    public function testImmutableUnset(): void
    {
        $this->expectException(BadMethodCallException::class);
        $announceList = $this->getAnnounceList();
        unset($announceList[0]);
    }
}
