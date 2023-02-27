<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Files;

use Arokettu\Torrent\Common\Attributes;
use Arokettu\Torrent\MetaVersion;
use Arokettu\Torrent\TorrentFile;
use Arokettu\Torrent\V2\File;
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
                piecesRootBin: base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4="),
                symlinkPath: null,
            ),
        ], $files);
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
                piecesRootBin: base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4="),
                symlinkPath: null,
            ),
            'file2.txt' => new File(
                name: 'file2.txt',
                path: ['file2.txt'],
                length: 6621341,
                attributes: new Attributes(''),
                piecesRootBin: base64_decode("W64kU2QHo/iMSgYP6thVUL0nPGqyH4/iZcrYEonjIyk="),
                symlinkPath: null,
            ),
            'file3.txt' => new File(
                name: 'file3.txt',
                path: ['file3.txt'],
                length: 6621335,
                attributes: new Attributes(''),
                piecesRootBin: base64_decode("5LnSj1BlgMSXaD9sLMbo8odfsnlSx5WVV1KOirR3zPk="),
                symlinkPath: null,
            ),
        ], $files);
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
                    piecesRootBin: base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4="),
                    symlinkPath: null,
                )
            ],
            'dir2' => [
                'file1.txt' => new File(
                    name: 'file1.txt',
                    path: ['dir2', 'file1.txt'],
                    length: 6621359,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4="),
                    symlinkPath: null,
                ),
                'file2.txt' => new File(
                    name: 'file2.txt',
                    path: ['dir2', 'file2.txt'],
                    length: 6621341,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode("W64kU2QHo/iMSgYP6thVUL0nPGqyH4/iZcrYEonjIyk="),
                    symlinkPath: null,
                ),
            ],
            'dir3' => [
                'file2.txt' => new File(
                    name: 'file2.txt',
                    path: ['dir3', 'file2.txt'],
                    length: 6621341,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode("W64kU2QHo/iMSgYP6thVUL0nPGqyH4/iZcrYEonjIyk="),
                    symlinkPath: null,
                ),
            ],
            'dir4' => [
                'aligned.txt' => new File(
                    name: 'aligned.txt',
                    path: ['dir4', 'aligned.txt'],
                    length: 6291456,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode("W6hGBiIEQEYDItCxP+pWgIWAOq7+TY5uszEwXru1rmE="),
                    symlinkPath: null,
                ),
            ],
            'dir5' => [
                'file3.txt' => new File(
                    name: 'file3.txt',
                    path: ['dir5', 'file3.txt'],
                    length: 6621335,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode("5LnSj1BlgMSXaD9sLMbo8odfsnlSx5WVV1KOirR3zPk="),
                    symlinkPath: null,
                ),
            ],
            'dir6' => [
                'exec.txt' => new File(
                    name: 'exec.txt',
                    path: ['dir6', 'exec.txt'],
                    length: 6621355,
                    attributes: new Attributes('x'),
                    piecesRootBin: base64_decode("uIBVltGapcVi0tqV+HSiFcMvYlhJNkU21OCR/3hZamw="),
                    symlinkPath: null,
                ),
            ],
        ], $files);
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
                    piecesRootBin: base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4="),
                    symlinkPath: null,
                ),
                '-22' => new File(
                    name: '-22',
                    path: ['2', '-22'],
                    length: 6621359,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4="),
                    symlinkPath: null,
                ),
                '0333' => new File(
                    name: '0333',
                    path: ['2', '0333'],
                    length: 6621359,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4="),
                    symlinkPath: null,
                ),
                // @phpcs:disable PHPCompatibility.Miscellaneous.ValidIntegers.HexNumericStringFound
                '0x4444' => new File(
                    name: '0x4444',
                    path: ['2', '0x4444'],
                    length: 6621359,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4="),
                    symlinkPath: null,
                ),
                // @phpcs:enable PHPCompatibility.Miscellaneous.ValidIntegers.HexNumericStringFound
                '1' => new File(
                    name: '1',
                    path: ['2', '1'],
                    length: 6621359,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4="),
                    symlinkPath: null,
                ),
            ],
            'dir' => [
                '1111' => new File(
                    name: '1111',
                    path: ['dir', '1111'],
                    length: 6621359,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4="),
                    symlinkPath: null,
                ),
                '222' => new File(
                    name: '222',
                    path: ['dir', '222'],
                    length: 6621359,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4="),
                    symlinkPath: null,
                ),
                '33' => new File(
                    name: '33',
                    path: ['dir', '33'],
                    length: 6621359,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4="),
                    symlinkPath: null,
                ),
                '4' => new File(
                    name: '4',
                    path: ['dir', '4'],
                    length: 6621359,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4="),
                    symlinkPath: null,
                ),
                'file1.txt' => new File(
                    name: 'file1.txt',
                    path: ['dir', 'file1.txt'],
                    length: 6621359,
                    attributes: new Attributes(''),
                    piecesRootBin: base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4="),
                    symlinkPath: null,
                ),
            ],
        ], $files);
    }
}
