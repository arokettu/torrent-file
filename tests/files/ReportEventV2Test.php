<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\Files;

use League\Event\EventDispatcher;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\ListenerProviderInterface;
use SandFox\Torrent\FileSystem\FileDataProgressEvent;
use SandFox\Torrent\MetaVersion;
use SandFox\Torrent\TorrentFile;

use const SandFox\Torrent\Tests\TEST_ROOT;

class ReportEventV2Test extends TestCase implements ListenerProviderInterface
{
    public function testReportEvent(): void
    {
        $eventDispatcher = new EventDispatcher($this);

        TorrentFile::fromPath(TEST_ROOT . '/data/files', [
            'version' => MetaVersion::V2,
        ], $eventDispatcher);
    }

    public function getListenersForEvent(object $event): iterable
    {
        return [
            FileDataProgressEvent::class => [$this, 'listener'],
        ];
    }

    public function listener(FileDataProgressEvent $event): void
    {
        self::assertEquals(6621335 + 6621341 + 6621359, $event->getTotal());

        $files = [
            'files',
            'file1.txt',
            'file2.txt',
            'file3.txt',
        ];

        self::assertContains($event->getFileName(), $files);
    }
}
