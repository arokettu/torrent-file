<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Files;

use Arokettu\Torrent\Exception\InvalidArgumentException;
use Arokettu\Torrent\TorrentFile\Common\Attributes;
use PHPUnit\Framework\TestCase;

class AttributesTest extends TestCase
{
    public function testMisc(): void
    {
        $attr = new Attributes('abcdefg');
        self::assertTrue($attr->d);
        self::assertTrue($attr->has('d'));
        self::assertFalse($attr->m);
        self::assertFalse($attr->has('m'));
    }

    public function testNone(): void
    {
        $attr = new Attributes('');

        self::assertFalse($attr->x);
        self::assertFalse($attr->executable);
        self::assertFalse($attr->has('x'));

        self::assertFalse($attr->l);
        self::assertFalse($attr->symlink);
        self::assertFalse($attr->has('l'));

        self::assertFalse($attr->p);
        self::assertFalse($attr->pad);
        self::assertFalse($attr->has('p'));
    }

    public function testExec(): void
    {
        $attr = new Attributes('x');
        self::assertTrue($attr->x);
        self::assertTrue($attr->executable);
        self::assertTrue($attr->has('x'));
    }

    public function testLink(): void
    {
        $attr = new Attributes('l');
        self::assertTrue($attr->l);
        self::assertTrue($attr->symlink);
        self::assertTrue($attr->has('l'));
    }

    public function testPad(): void
    {
        $attr = new Attributes('p');
        self::assertTrue($attr->p);
        self::assertTrue($attr->pad);
        self::assertTrue($attr->has('p'));
    }

    public function testHasOneCharOnly(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Attribute name must be 1 character long');

        (new Attributes('xx'))->has('xx');
    }

    public function testAttrOneCharOnly(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Attribute name must be 1 character long');

        (new Attributes('xx'))->xx;
    }
}
