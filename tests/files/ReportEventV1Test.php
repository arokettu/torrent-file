<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Files;

use Arokettu\Torrent\FileSystem\FileDataProgressEvent;
use Arokettu\Torrent\MetaVersion;
use Arokettu\Torrent\TorrentFile;
use League\Event\EventDispatcher;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;

use const Arokettu\Torrent\Tests\TEST_ROOT;

class ReportEventV1Test extends TestCase implements ListenerProviderInterface
{
    private int $done;

    public function testReportEvent(): void
    {
        $eventDispatcher = new EventDispatcher($this);

        $this->done = 0;

        TorrentFile::fromPath(
            TEST_ROOT . '/data/files',
            eventDispatcher:  $eventDispatcher,
            version: MetaVersion::V1,
        );
    }

    public function getListenersForEvent(object $event): iterable
    {
        return [
            FileDataProgressEvent::class => [$this, 'listener'],
        ];
    }

    public function listener(FileDataProgressEvent $event): void
    {
        self::assertEquals($this->done, $event->done);
        self::assertEquals(6621335 + 6621341 + 6621359, $event->total);

        if ($this->done === 6621335 + 6621341 + 6621359) { // total
            $file = 'files';
        } elseif ($this->done > 6621341 + 6621359) { // file2 + file1 size
            $file = 'file3.txt';
        } elseif ($this->done > 6621359) { // file1 size
            $file = 'file2.txt';
        } elseif ($this->done > 0) {
            $file = 'file1.txt';
        } else {
            $file = 'files';
        }

        self::assertEquals($file, $event->fileName);

        $this->done += 512 * 1024;
        if ($this->done > 6621335 + 6621341 + 6621359) {
            $this->done = 6621335 + 6621341 + 6621359;
        }
    }

    public function testGetters(): void
    {
        // mostly for coverage
        $event = new FileDataProgressEvent(100, 50, 'test.txt');

        self::assertEquals(100, $event->total);
        self::assertEquals(50, $event->done);
        self::assertEquals('test.txt', $event->fileName);
    }
}
