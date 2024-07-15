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

class ReportEventV2Test extends TestCase implements ListenerProviderInterface
{
    public function testReportEvent(): void
    {
        $eventDispatcher = @new EventDispatcher($this); // external deprecation

        TorrentFile::fromPath(
            TEST_ROOT . '/data/files',
            eventDispatcher: $eventDispatcher,
            version: MetaVersion::V2,
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
        self::assertEquals(6621335 + 6621341 + 6621359, $event->total);

        $files = [
            'files',
            'file1.txt',
            'file2.txt',
            'file3.txt',
        ];

        self::assertContains($event->fileName, $files);
    }
}
