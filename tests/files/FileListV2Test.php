<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Files;

use Arokettu\Bencode\Bencode;
use Arokettu\Torrent\Exception\RuntimeException;
use Arokettu\Torrent\MetaVersion;
use Arokettu\Torrent\TorrentFile;
use Arokettu\Torrent\TorrentFile\Common\Attributes;
use Arokettu\Torrent\TorrentFile\V2\File;
use PHPUnit\Framework\TestCase;

use function Arokettu\Torrent\Tests\recursive_iterator_to_array;

use const Arokettu\Torrent\Tests\TEST_ROOT;

class FileListV2Test extends TestCase
{
    public function testSingleFile(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/files/file1.txt',
            version: MetaVersion::V2,
        ); // approx 6 mb

        $files = recursive_iterator_to_array($torrent->v2()->getFileTree());

        self::assertEquals([
            'file1.txt' => new File(
                name: 'file1.txt',
                path: ['file1.txt'],
                length: 6621359,
                attributes: new Attributes(''),
                piecesRootBin: base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                symlinkPath: null,
            ),
        ], $files);
        self::assertEquals(1, \count($torrent->v2()->getFileTree()));
    }

    public function testMultiFile(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/files',
            version: MetaVersion::V2
        ); // approx 19 mb

        $files = recursive_iterator_to_array($torrent->v2()->getFileTree());

        self::assertEquals([
            'file1.txt' => new File(
                name: 'file1.txt',
                path: ['file1.txt'],
                length: 6621359,
                attributes: new Attributes(''),
                piecesRootBin: base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                symlinkPath: null,
            ),
            'file2.txt' => new File(
                name: 'file2.txt',
                path: ['file2.txt'],
                length: 6621341,
                attributes: new Attributes(''),
                piecesRootBin: base64_decode('W64kU2QHo/iMSgYP6thVUL0nPGqyH4/iZcrYEonjIyk='),
                symlinkPath: null,
            ),
            'file3.txt' => new File(
                name: 'file3.txt',
                path: ['file3.txt'],
                length: 6621335,
                attributes: new Attributes(''),
                piecesRootBin: base64_decode('5LnSj1BlgMSXaD9sLMbo8odfsnlSx5WVV1KOirR3zPk='),
                symlinkPath: null,
            ),
        ], $files);
        self::assertEquals(3, \count($torrent->v2()->getFileTree()));
    }

    public function testFiles2(): void
    {
        $torrent = TorrentFile::fromPath(TEST_ROOT . '/data/files2', version: MetaVersion::V2);

        $files = recursive_iterator_to_array($torrent->v2()->getFileTree());

        self::assertEquals([
            'dir1' => [
                'file1.txt' => new File(
                    name: 'file1.txt',
                    path: ['dir1', 'file1.txt'],
                    length: 6621359,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                    symlinkPath: null,
                )
            ],
            'dir2' => [
                'file1.txt' => new File(
                    name: 'file1.txt',
                    path: ['dir2', 'file1.txt'],
                    length: 6621359,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                    symlinkPath: null,
                ),
                'file2.txt' => new File(
                    name: 'file2.txt',
                    path: ['dir2', 'file2.txt'],
                    length: 6621341,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode('W64kU2QHo/iMSgYP6thVUL0nPGqyH4/iZcrYEonjIyk='),
                    symlinkPath: null,
                ),
            ],
            'dir3' => [
                'file2.txt' => new File(
                    name: 'file2.txt',
                    path: ['dir3', 'file2.txt'],
                    length: 6621341,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode('W64kU2QHo/iMSgYP6thVUL0nPGqyH4/iZcrYEonjIyk='),
                    symlinkPath: null,
                ),
            ],
            'dir4' => [
                'aligned.txt' => new File(
                    name: 'aligned.txt',
                    path: ['dir4', 'aligned.txt'],
                    length: 6291456,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode('W6hGBiIEQEYDItCxP+pWgIWAOq7+TY5uszEwXru1rmE='),
                    symlinkPath: null,
                ),
            ],
            'dir5' => [
                'file3.txt' => new File(
                    name: 'file3.txt',
                    path: ['dir5', 'file3.txt'],
                    length: 6621335,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode('5LnSj1BlgMSXaD9sLMbo8odfsnlSx5WVV1KOirR3zPk='),
                    symlinkPath: null,
                ),
            ],
            'dir6' => [
                'exec.txt' => new File(
                    name: 'exec.txt',
                    path: ['dir6', 'exec.txt'],
                    length: 6621355,
                    attributes: new Attributes('x'),
                    piecesRootBin: base64_decode('uIBVltGapcVi0tqV+HSiFcMvYlhJNkU21OCR/3hZamw='),
                    symlinkPath: null,
                ),
            ],
        ], $files);
        self::assertEquals(6, \count($torrent->v2()->getFileTree()));
    }

    public function testFilesNumericNames(): void
    {
        // the only case when numeric keys can occur in torrent file
        $torrent = TorrentFile::fromPath(TEST_ROOT . '/data/4444', version: MetaVersion::V2);

        $files = recursive_iterator_to_array($torrent->v2()->getFileTree());

        self::assertEquals([
            '2' => [
                'file2.txt' => new File(
                    name: 'file2.txt',
                    path: ['2', 'file2.txt'],
                    length: 6621359,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                    symlinkPath: null,
                ),
                '-22' => new File(
                    name: '-22',
                    path: ['2', '-22'],
                    length: 6621359,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                    symlinkPath: null,
                ),
                '0333' => new File(
                    name: '0333',
                    path: ['2', '0333'],
                    length: 6621359,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                    symlinkPath: null,
                ),
                // @phpcs:disable PHPCompatibility.Miscellaneous.ValidIntegers.HexNumericStringFound
                '0x4444' => new File(
                    name: '0x4444',
                    path: ['2', '0x4444'],
                    length: 6621359,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                    symlinkPath: null,
                ),
                // @phpcs:enable PHPCompatibility.Miscellaneous.ValidIntegers.HexNumericStringFound
                '1' => new File(
                    name: '1',
                    path: ['2', '1'],
                    length: 6621359,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                    symlinkPath: null,
                ),
            ],
            'dir' => [
                '1111' => new File(
                    name: '1111',
                    path: ['dir', '1111'],
                    length: 6621359,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                    symlinkPath: null,
                ),
                '222' => new File(
                    name: '222',
                    path: ['dir', '222'],
                    length: 6621359,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                    symlinkPath: null,
                ),
                '33' => new File(
                    name: '33',
                    path: ['dir', '33'],
                    length: 6621359,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                    symlinkPath: null,
                ),
                '4' => new File(
                    name: '4',
                    path: ['dir', '4'],
                    length: 6621359,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                    symlinkPath: null,
                ),
                'file1.txt' => new File(
                    name: 'file1.txt',
                    path: ['dir', 'file1.txt'],
                    length: 6621359,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                    symlinkPath: null,
                ),
            ],
        ], $files);
        self::assertEquals(2, \count($torrent->v2()->getFileTree()));
    }

    public function testIgnoreDirAttrHash(): void
    {
        $data = [
            'info' => [
                'meta version' => 2,
                'file tree' => [
                    'dir1' => [
                        '' => [
                            'some attribute for a directory, never seen one in the wilds but it\'s possible' => 'abc',
                            // no 'length' - it's important
                        ],
                        'file1' => [
                            '' => [
                                'length' => 123456,
                                'pieces root' => base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                            ]
                        ],
                    ],
                ],
            ],
        ];

        $torrent = TorrentFile::loadFromString(Bencode::encode($data));
        $files = recursive_iterator_to_array($torrent->v2()->getFileTree());

        self::assertEquals([
            'dir1' => [
                'file1' => new File(
                    name: 'file1',
                    path: ['dir1', 'file1'],
                    length: 123456,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                    symlinkPath: null,
                ),
            ],
        ], $files);
    }

    public function testNoChildFilesInFile(): void
    {
        $data = [
            'info' => [
                'meta version' => 2,
                'file tree' => [
                    'dir1' => [
                        'file1' => [
                            '' => [
                                'length' => 123456,
                                'pieces root' => base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                            ],
                            'child file' => [
                                '' => [
                                    'length' => 123456,
                                    'pieces root' => base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $torrent = TorrentFile::loadFromString(Bencode::encode($data));
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Invalid node: file cannot contain child files');
        $torrent->v2()->getFileTree();
    }

    public function testReadSymlink(): void
    {
        $data = [
            'info' => [
                'meta version' => 2,
                'file tree' => [
                    'dir1' => [
                        'file1' => [
                            '' => [
                                'length' => 123456,
                                'pieces root' => base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                            ]
                        ],
                        'file2' => [
                            '' => [
                                'length' => 0,
                                'attr' => 'l',
                                'symlink path' => ['dir1', 'file1'],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $torrent = TorrentFile::loadFromString(Bencode::encode($data));
        $files = recursive_iterator_to_array($torrent->v2()->getFileTree());

        self::assertEquals([
            'dir1' => [
                'file1' => new File(
                    name: 'file1',
                    path: ['dir1', 'file1'],
                    length: 123456,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                    symlinkPath: null,
                ),
                'file2' => new File(
                    name: 'file2',
                    path: ['dir1', 'file2'],
                    length: 0,
                    attributes: new Attributes('l'),
                    piecesRootBin: null,
                    symlinkPath: ['dir1', 'file1'],
                ),
            ],
        ], $files);
    }

    public function testSymlinkMustHavePath(): void
    {
        $data = [
            'info' => [
                'meta version' => 2,
                'file tree' => [
                    'dir1' => [
                        'file1' => [
                            '' => [
                                'length' => 123456,
                                'pieces root' => base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                            ]
                        ],
                        'file2' => [
                            '' => [
                                'length' => 0,
                                'attr' => 'l',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $torrent = TorrentFile::loadFromString(Bencode::encode($data));
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Invalid symlink: missing link path');
        recursive_iterator_to_array($torrent->v2()->getFileTree());
    }

    public function testSymlinkMustBe0Length(): void
    {
        $data = [
            'info' => [
                'meta version' => 2,
                'file tree' => [
                    'dir1' => [
                        'file1' => [
                            '' => [
                                'length' => 123456,
                                'pieces root' => base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                            ]
                        ],
                        'file2' => [
                            '' => [
                                'length' => 123,
                                'attr' => 'l',
                                'symlink path' => ['dir1', 'file1'],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $torrent = TorrentFile::loadFromString(Bencode::encode($data));
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Invalid symlink: must be 0 length');
        recursive_iterator_to_array($torrent->v2()->getFileTree());
    }
}
