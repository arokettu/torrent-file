<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Files;

use Arokettu\Bencode\Bencode;
use Arokettu\Torrent\Exception\RuntimeException;
use Arokettu\Torrent\MetaVersion;
use Arokettu\Torrent\TorrentFile;
use Arokettu\Torrent\TorrentFile\Common\Attributes;
use Arokettu\Torrent\TorrentFile\V1\File;
use PHPUnit\Framework\TestCase;

use const Arokettu\Torrent\Tests\TEST_ROOT;

class FileListV1Test extends TestCase
{
    public function testSingleFile(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/files/file1.txt',
            version: MetaVersion::V1,
            forceMultifile: false,
        ); // approx 6 mb

        $files = iterator_to_array($torrent->v1()->getFiles());

        self::assertEquals([
            new File(
                path: ['file1.txt'],
                length: 6621359,
                attributes: new Attributes(''),
                sha1bin: base64_decode('FLpF01Q+gHDBdrRmIDPqQmKaYgQ='),
                symlinkPath: null,
            ),
        ], $files);
        self::assertEquals(1, \count($torrent->v1()->getFiles()));
    }

    public function testSingleFileForceSetAttr(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/files/file1.txt',
            version: MetaVersion::V1,
            forceMultifile: false,
        ); // approx 6 mb

        // get a writable copy
        $data = Bencode::decode(Bencode::encode($torrent));
        $data['info']['attr'] = 'x';
        $torrent = TorrentFile::loadFromString(Bencode::encode($data));

        $files = iterator_to_array($torrent->v1()->getFiles());

        self::assertEquals([
            new File(
                path: ['file1.txt'],
                length: 6621359,
                attributes: new Attributes('x'),
                sha1bin: base64_decode('FLpF01Q+gHDBdrRmIDPqQmKaYgQ='),
                symlinkPath: null,
            ),
        ], $files);
        self::assertEquals(1, \count($torrent->v1()->getFiles()));
    }

    public function testSingleFileMissingName(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/files/file1.txt',
            version: MetaVersion::V1,
            forceMultifile: false,
        );

        // get a writable copy
        $data = Bencode::decode(Bencode::encode($torrent));
        unset($data['info']['name']);
        $torrent = TorrentFile::loadFromString(Bencode::encode($data));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid single-file torrent file: name is not set');
        $torrent->v1()->getFiles();
    }

    public function testSingleFileMissingLength(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/files/file1.txt',
            version: MetaVersion::V1,
            forceMultifile: false,
        );

        // get a writable copy
        $data = Bencode::decode(Bencode::encode($torrent));
        unset($data['info']['length']);
        $torrent = TorrentFile::loadFromString(Bencode::encode($data));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid single-file torrent file: length is not set');
        $torrent->v1()->getFiles();
    }

    public function testSingleFileForceMulti(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/files/file1.txt',
            version: MetaVersion::V1,
            forceMultifile: true,
        ); // approx 6 mb

        $files = iterator_to_array($torrent->v1()->getFiles());

        self::assertEquals([
            new File(
                path: ['file1.txt'],
                length: 6621359,
                attributes: new Attributes(''),
                sha1bin: base64_decode('FLpF01Q+gHDBdrRmIDPqQmKaYgQ='),
                symlinkPath: null,
            ),
        ], $files);
        self::assertEquals(1, \count($torrent->v1()->getFiles()));
    }

    public function testMultiFile(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/files',
            version: MetaVersion::V1,
        ); // approx 19 mb

        $files = iterator_to_array($torrent->v1()->getFiles());

        self::assertEquals([
            new File(
                path: ['file1.txt'],
                length: 6621359,
                attributes: new Attributes(''),
                sha1bin: base64_decode('FLpF01Q+gHDBdrRmIDPqQmKaYgQ='),
                symlinkPath: null,
            ),
            new File(
                path: ['file2.txt'],
                length: 6621341,
                attributes: new Attributes(''),
                sha1bin: base64_decode('JToK2HdRS+5VKZCu8WhvbV9a9KY='),
                symlinkPath: null,
            ),
            new File(
                path: ['file3.txt'],
                length: 6621335,
                attributes: new Attributes(''),
                sha1bin: base64_decode('WW5Dv31hzse3rO95vQfVTk7M3lg='),
                symlinkPath: null,
            ),
        ], $files);
        self::assertEquals(3, \count($torrent->v1()->getFiles()));
    }

    public function testMultiFileWithPads(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/files',
            version: MetaVersion::V1,
            pieceAlign: true,
        ); // approx 19 mb

        $expected = [
            new File(
                path: ['file1.txt'],
                length: 6621359,
                attributes: new Attributes(''),
                sha1bin: base64_decode('FLpF01Q+gHDBdrRmIDPqQmKaYgQ='),
                symlinkPath: null,
            ),
            new File(
                path: ['.pad', '194385'],
                length: 194385,
                attributes: new Attributes('p'),
                sha1bin: null,
                symlinkPath: null,
            ),
            new File(
                path: ['file2.txt'],
                length: 6621341,
                attributes: new Attributes(''),
                sha1bin: base64_decode('JToK2HdRS+5VKZCu8WhvbV9a9KY='),
                symlinkPath: null,
            ),
            new File(
                path: ['.pad', '194403'],
                length: 194403,
                attributes: new Attributes('p'),
                sha1bin: null,
                symlinkPath: null,
            ),
            new File(
                path: ['file3.txt'],
                length: 6621335,
                attributes: new Attributes(''),
                sha1bin: base64_decode('WW5Dv31hzse3rO95vQfVTk7M3lg='),
                symlinkPath: null,
            ),
        ];

        $files = iterator_to_array($torrent->v1()->getFiles()->getIterator(false));
        self::assertEquals($expected, $files);
        self::assertEquals(5, $torrent->v1()->getFiles()->count(false));

        unset($expected[1], $expected[3]);

        $files = iterator_to_array($torrent->v1()->getFiles());
        self::assertEquals($expected, $files);
        self::assertEquals(3, \count($torrent->v1()->getFiles()));
    }

    public function testFiles2(): void
    {
        $torrent = TorrentFile::fromPath(TEST_ROOT . '/data/files2', version: MetaVersion::V1);

        $files = iterator_to_array($torrent->v1()->getFiles());

        self::assertEquals([
            new File(
                path: ['dir1', 'file1.txt'],
                length: 6621359,
                attributes: new Attributes(''),
                sha1bin: base64_decode('FLpF01Q+gHDBdrRmIDPqQmKaYgQ='),
                symlinkPath: null,
            ),
            new File(
                path: ['dir2', 'file1.txt'],
                length: 6621359,
                attributes: new Attributes(''),
                sha1bin: base64_decode('FLpF01Q+gHDBdrRmIDPqQmKaYgQ='),
                symlinkPath: null,
            ),
            new File(
                path: ['dir2', 'file2.txt'],
                length: 6621341,
                attributes: new Attributes(''),
                sha1bin: base64_decode('JToK2HdRS+5VKZCu8WhvbV9a9KY='),
                symlinkPath: null,
            ),
            new File(
                path: ['dir3', 'file2.txt'],
                length: 6621341,
                attributes: new Attributes(''),
                sha1bin: base64_decode('JToK2HdRS+5VKZCu8WhvbV9a9KY='),
                symlinkPath: null,
            ),
            new File(
                path: ['dir4', 'aligned.txt'],
                length: 6291456,
                attributes: new Attributes(''),
                sha1bin: base64_decode('8uHdutKp152UxbBUEr66/UNo/I0='),
                symlinkPath: null,
            ),
            new File(
                path: ['dir5', 'file3.txt'],
                length: 6621335,
                attributes: new Attributes(''),
                sha1bin: base64_decode('WW5Dv31hzse3rO95vQfVTk7M3lg='),
                symlinkPath: null,
            ),
            new File(
                path: ['dir6', 'exec.txt'],
                length: 6621355,
                attributes: new Attributes('x'),
                sha1bin: base64_decode('PLesPfBgCmcfBdyu9k95eUh8sfs='),
                symlinkPath: null,
            ),
        ], $files);
        self::assertEquals(7, \count($torrent->v1()->getFiles()));
    }

    public function testLengthMustBePresent(): void
    {
        $data = [
            'info' => [
                'files' => [
                    [
                        'path' => ['dir1', 'file1'],
                    ],
                ],
                'pieces' => '...', // not read here
            ]
        ];

        $torrent = TorrentFile::loadFromString(Bencode::encode($data));
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Invalid file: missing length');
        $torrent->v1()->getFiles();
    }

    public function testReadSymlink(): void
    {
        $data = [
            'info' => [
                'files' => [
                    [
                        'path' => ['dir1', 'file1'],
                        'length' => 123456,
                    ],
                    [
                        'path' => ['dir1', 'file2'],
                        'length' => 0,
                        'attr' => 'l',
                        'symlink path' => ['dir1', 'file1'],
                    ],
                ],
                'pieces' => '...', // not read here
            ]
        ];

        $torrent = TorrentFile::loadFromString(Bencode::encode($data));

        self::assertEquals([
            new File(
                path: ['dir1', 'file1'],
                length: 123456,
                attributes: new Attributes(''),
                sha1bin: null,
                symlinkPath: null,
            ),
            new File(
                path: ['dir1', 'file2'],
                length: 0,
                attributes: new Attributes('l'),
                sha1bin: null,
                symlinkPath: ['dir1', 'file1'],
            ),
        ], [...$torrent->v1()->getFiles()]);
    }

    public function testSymlinkTolerantToMissingLength(): void
    {
        $data = [
            'info' => [
                'files' => [
                    [
                        'path' => ['dir1', 'file1'],
                        'length' => 123456,
                    ],
                    [
                        'path' => ['dir1', 'file2'],
                        'attr' => 'l',
                        'symlink path' => ['dir1', 'file1'],
                    ],
                ],
                'pieces' => '...', // not read here
            ]
        ];

        $torrent = TorrentFile::loadFromString(Bencode::encode($data));

        self::assertEquals([
            new File(
                path: ['dir1', 'file1'],
                length: 123456,
                attributes: new Attributes(''),
                sha1bin: null,
                symlinkPath: null,
            ),
            new File(
                path: ['dir1', 'file2'],
                length: 0,
                attributes: new Attributes('l'),
                sha1bin: null,
                symlinkPath: ['dir1', 'file1'],
            ),
        ], [...$torrent->v1()->getFiles()]);
    }

    public function testSymlinkMustBe0Length(): void
    {
        $data = [
            'info' => [
                'files' => [
                    [
                        'path' => ['dir1', 'file1'],
                        'length' => 123456,
                    ],
                    [
                        'path' => ['dir1', 'file2'],
                        'length' => 123,
                        'attr' => 'l',
                        'symlink path' => ['dir1', 'file1'],
                    ],
                ],
                'pieces' => '...', // not read here
            ]
        ];

        $torrent = TorrentFile::loadFromString(Bencode::encode($data));
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Invalid symlink: must be 0 length');
        $torrent->v1()->getFiles();
    }
}
