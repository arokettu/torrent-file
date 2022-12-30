<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Files;

use Arokettu\Torrent\MetaVersion;
use Arokettu\Torrent\TorrentFile;
use PHPUnit\Framework\TestCase;

use const Arokettu\Torrent\Tests\TEST_ROOT;

class CreateFileV2FeaturesTest extends TestCase
{
    public function testFiles2(): void
    {
        // test info dict on default settings
        $torrent = TorrentFile::fromPath(TEST_ROOT . '/data/files2', version: MetaVersion::V2);
        $info = $torrent->getRawData()['info'];

//        echo export_test_data($info);
        $this->assertEquals(
            '95a5d63fe33c4e1651856f1f2ecb4819a02ab5ddce007af5fa1afe225dd6a719',
            $torrent->getInfoHash()
        );
        $this->assertEquals(
            [
                'file tree' => [
                    'dir1' => [
                        'file1.txt' => [
                            '' => [
                                'length' => 6621359,
                                'pieces root' => base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4="),
                            ],
                        ],
                    ],
                    'dir2' => [
                        'file1.txt' => [
                            '' => [
                                'length' => 6621359,
                                'pieces root' => base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4="),
                            ],
                        ],
                        'file2.txt' => [
                            '' => [
                                'length' => 6621341,
                                'pieces root' => base64_decode("W64kU2QHo/iMSgYP6thVUL0nPGqyH4/iZcrYEonjIyk="),
                            ],
                        ],
                    ],
                    'dir3' => [
                        'file2.txt' => [
                            '' => [
                                'length' => 6621341,
                                'pieces root' => base64_decode("W64kU2QHo/iMSgYP6thVUL0nPGqyH4/iZcrYEonjIyk="),
                            ],
                        ],
                    ],
                    'dir4' => [
                        'aligned.txt' => [
                            '' => [
                                'length' => 6291456,
                                'pieces root' => base64_decode("W6hGBiIEQEYDItCxP+pWgIWAOq7+TY5uszEwXru1rmE="),
                            ],
                        ],
                    ],
                    'dir5' => [
                        'file3.txt' => [
                            '' => [
                                'length' => 6621335,
                                'pieces root' => base64_decode("5LnSj1BlgMSXaD9sLMbo8odfsnlSx5WVV1KOirR3zPk="),
                            ],
                        ],
                    ],
                    'dir6' => [
                        'exec.txt' => [
                            '' => [
                                'attr' => 'x',
                                'length' => 6621355,
                                'pieces root' => base64_decode("uIBVltGapcVi0tqV+HSiFcMvYlhJNkU21OCR/3hZamw="),
                            ],
                        ],
                    ],
                ],
                'meta version' => 2,
                'name' => 'files2',
                'piece length' => 524288,
            ],
            $info
        );
    }

    public function testExecutable(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/files2',
            version: MetaVersion::V2,
            detectExec: true,
        );

        $info = $torrent->getRawData()['info'];

        $xfile = $info['file tree']['dir6']['exec.txt'][''];

        self::assertStringContainsString('x', $xfile['attr']);
    }

    public function testExecutableDisabled(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/files2',
            version: MetaVersion::V2,
            detectExec: false,
        );

        $info = $torrent->getRawData()['info'];

        $xfile = $info['file tree']['dir6']['exec.txt'][''];

        self::assertStringNotContainsString('x', $xfile['attr'] ?? '');
    }

    public function testSymlinks(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/files2',
            version: MetaVersion::V2,
            detectSymlinks: true,
        );

        $info = $torrent->getRawData()['info'];

//        echo export_test_data($info);
        $this->assertEquals(
            [
                'file tree' => [
                    'dir1' => [
                        'file1.txt' => [ // not link!
                            '' => [
                                'length' => 6621359,
                                'pieces root' => base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4="),
                            ],
                        ],
                    ],
                    'dir2' => [
                        'file1.txt' => [ // link!
                            '' => [
                                'attr' => 'l',
                                'symlink path' => [
                                    'dir1',
                                    'file1.txt',
                                ],
                            ],
                        ],
                        'file2.txt' => [ // link!
                            '' => [
                                'attr' => 'l',
                                'symlink path' => [
                                    'dir3',
                                    'file2.txt',
                                ],
                            ],
                        ],
                    ],
                    'dir3' => [
                        'file2.txt' => [ // not link!
                            '' => [
                                'length' => 6621341,
                                'pieces root' => base64_decode("W64kU2QHo/iMSgYP6thVUL0nPGqyH4/iZcrYEonjIyk="),
                            ],
                        ],
                    ],
                    'dir4' => [
                        'aligned.txt' => [
                            '' => [
                                'length' => 6291456,
                                'pieces root' => base64_decode("W6hGBiIEQEYDItCxP+pWgIWAOq7+TY5uszEwXru1rmE="),
                            ],
                        ],
                    ],
                    'dir5' => [
                        'file3.txt' => [ // not link!
                            '' => [
                                'length' => 6621335,
                                'pieces root' => base64_decode("5LnSj1BlgMSXaD9sLMbo8odfsnlSx5WVV1KOirR3zPk="),
                            ],
                        ],
                    ],
                    'dir6' => [
                        'exec.txt' => [
                            '' => [
                                'attr' => 'x',
                                'length' => 6621355,
                                'pieces root' => base64_decode("uIBVltGapcVi0tqV+HSiFcMvYlhJNkU21OCR/3hZamw="),
                            ],
                        ],
                    ],
                ],
                'meta version' => 2,
                'name' => 'files2',
                'piece length' => 524288,
            ],
            $info
        );
    }
}
