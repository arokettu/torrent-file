<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\Files;

use PHPUnit\Framework\TestCase;
use SandFox\Torrent\Exception\InvalidArgumentException;
use SandFox\Torrent\Exception\PathNotFoundException;
use SandFox\Torrent\MetaVersion;
use SandFox\Torrent\TorrentFile;

use function SandFox\Torrent\Tests\build_magnet_link;

use const SandFox\Torrent\Tests\TEST_ROOT;

class CreateFileV1Test extends TestCase
{
    public function testSingleFile(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/files/file1.txt',
            version: MetaVersion::V1,
        ); // approx 6 mb

        self::assertEquals('3ab5a1739bd320333898510a6cec900a5e6acb7d', $torrent->getInfoHash());
//        echo export_test_data($torrent->getRawData()['info']);
        self::assertEquals([
            'length' => 6621359,
            'name' => 'file1.txt',
            'piece length' => 524288,
            'pieces' => base64_decode(<<<PIECES
                UA6+qBSqwP7uJvTrqHs5iSp5mUcYJfIZ0wAyzY2UHsZoDGPTMYeNeHBiUmrKwus8K15+gprxhB4ZmcoA/4vOAEQnc
                UHAAkG2ApyqUloDAZ8XO3ktOMTUiQudWYbF+C7vrrYcJZZSA1ah8mNroUK9GEhJ/3tU40U4gfAgqRjk+AYay689QD
                M/8hpiYYegLmNYntD0erSEXD7G9Fy4DT1SOMM4lHtUQsC+7erlN+apGisf4erLaK2bGTgKsbDwETNk115guP75Osx
                O499nbjEf7uzNnu+SVo3wmeoI5/mx1jV2iihYK4Ow/iJL7yq2CUruoTvVHnSPqq4c3I2T5nT3YPQqLBc=
                PIECES),
            'sha1' => base64_decode("FLpF01Q+gHDBdrRmIDPqQmKaYgQ="),
        ], $torrent->getRawData()['info']);
        self::assertEquals(260, \strlen($torrent->getRawData()['info']['pieces'])); // 13 chunks
        self::assertEquals('file1.txt', $torrent->getDisplayName());
        self::assertEquals('file1.txt.torrent', $torrent->getFileName());
        self::assertFalse($torrent->isDirectory());

        self::assertEquals(
            build_magnet_link([
                'xt=urn:btih:3ab5a1739bd320333898510a6cec900a5e6acb7d',
                'dn=file1.txt',
            ]),
            $torrent->getMagnetLink()
        );
    }

    public function testMultipleFiles(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/files',
            version: MetaVersion::V1,
        ); // approx 19 mb

        self::assertEquals('e3bfb18c606631c472b7ba1813bc96c7f748b098', $torrent->getInfoHash());
//        echo export_test_data($torrent->getRawData()['info']);
        self::assertEquals(
            [
                'files' => [
                    [
                        'length' => 6621359,
                        'path' => ['file1.txt'],
                        'sha1' => base64_decode("FLpF01Q+gHDBdrRmIDPqQmKaYgQ="),
                    ],
                    [
                        'length' => 6621341,
                        'path' => ['file2.txt'],
                        'sha1' => base64_decode("JToK2HdRS+5VKZCu8WhvbV9a9KY="),
                    ],
                    [
                        'length' => 6621335,
                        'path' => ['file3.txt'],
                        'sha1' => base64_decode("WW5Dv31hzse3rO95vQfVTk7M3lg="),
                    ],
                ],
                'name' => 'files',
                'piece length' => 524288,
                'pieces' => base64_decode(<<<PIECES
                    UA6+qBSqwP7uJvTrqHs5iSp5mUcYJfIZ0wAyzY2UHsZoDGPTMYeNeHBiUmrKwus8K15+gprxhB4Zmco
                    A/4vOAEQncUHAAkG2ApyqUloDAZ8XO3ktOMTUiQudWYbF+C7vrrYcJZZSA1ah8mNroUK9GEhJ/3tU40
                    U4gfAgqRjk+AYay689QDM/8hpiYYegLmNYntD0erSEXD7G9Fy4DT1SOMM4lHtUQsC+7erlN+apGisf4
                    erLaK2bGTgKsbDwETNk115guP75OsxO499nbjEf7uzNnu+SVo3wmeoI5/mx1jV2iihYK4Ow/iJL7yq2
                    CUruihk4M/lebvPJnEfBTUvdCoFQgS9jQ2WiZD5/gYl0SBcWwh9FtIKZAk4YSRTGIeRQ3uXrskTU0He
                    9RBdgQaOZ8ABWE4SMUURL+XDW70fjiUa1tu8UBF8CXXhn79MxF6KXE582EpS4vihoyDRFGDm/LmkZzD
                    sXFlSbSLDLkJQcQUKYcAiHDPhw/x3cDE4nFCeXrDD5KqFoSU00GjSJhZnOIWGP/217S/WL3BdekItM3
                    HLx2Bq/QQUHmTimqOmC1s5vTkQBM1hhc+zts7/oBz5+Kz+9EDdgyjBCKs/x/fYk74HyKKwBz97lPpf+
                    c+WUc+GkJMQO1S/BEdpL4G3wghtWdDVw9ak5u52mDLvclL3Xg2AmEBdMe8hRw7u6DrBeDQYoc95Fwp8
                    U3G/zEkOyapShH77K83cwoRUlir7GErzIwMQ6tBHnucv+xNbuZzEh30maSnRgk96BM7d+KYbwbisqJs
                    TeQ+ZhA8oYwi+/2i0Cwd8aiIXZpWVvciVjl+cImI+3YerfVB/wbTvWkfplvnorW/jk0kJRLWBuvJe1e
                    qtqTPOyTYM+oYceqWoWUsC+GaLXgNJ1x4lif2YAZ6jsTPn1DWAAk1jRaSop4JWdmzrwa89eMEutd/O0
                    iLX3I5LKK/vm4uRtJPu835z3zR1JrRuzW3J6TEHvbYqbr5L2uI573DUhKccSXsglMw==
                    PIECES),
            ],
            $torrent->getRawData()['info'],
        );
        self::assertEquals(760, \strlen($torrent->getRawData()['info']['pieces'])); // 38 chunks
        self::assertEquals('files', $torrent->getDisplayName());
        self::assertEquals('files.torrent', $torrent->getFileName());
        self::assertTrue($torrent->isDirectory());

        self::assertEquals(
            build_magnet_link([
                'xt=urn:btih:e3bfb18c606631c472b7ba1813bc96c7f748b098',
                'dn=files',
            ]),
            $torrent->getMagnetLink()
        );
    }

    public function testMultipleFiles1MB(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/files',
            version: MetaVersion::V1,
            pieceLength: 1024 * 1024, // 1mb chunk
        ); // approx 19 mb

        self::assertEquals('8d7b1593175abfa6563f7c8de082e5c46b3d1292', $torrent->getInfoHash());
//        echo export_test_data($torrent->getRawData()['info']);
        self::assertEquals(
            [
                'files' => [
                    [
                        'length' => 6621359,
                        'path' => ['file1.txt'],
                        'sha1' => base64_decode("FLpF01Q+gHDBdrRmIDPqQmKaYgQ="),
                    ],
                    [
                        'length' => 6621341,
                        'path' => ['file2.txt'],
                        'sha1' => base64_decode("JToK2HdRS+5VKZCu8WhvbV9a9KY="),
                    ],
                    [
                        'length' => 6621335,
                        'path' => ['file3.txt'],
                        'sha1' => base64_decode("WW5Dv31hzse3rO95vQfVTk7M3lg="),
                    ],
                ],
                'name' => 'files',
                'piece length' => 1048576,
                'pieces' => base64_decode(<<<PIECES
                    5JIl0MITGTvKxyJ7ndx9QNkYzWd4I+jicVKLTzAhWBTqecWTVJRE4QIeSGkDiMQ6xUHPMPdT6Fs448lo2MGIc9P0cEZlXG
                    aJkLoHvVOwZ0MoOBY3CWtnO9zTrQVO8nS48yP+d/uUB+Uf/rYn8nxmtOLo61BK3gCrBJUIVe1vija2EY941niayzcjcr22
                    Xp1hUIaRkX4/QwyoISzCTE4UF7BVl8xrXTkYYFxr4ZibgUJb8GmnKIX5B7az2i3JcZZzCOvGjyf/u1SRFIEv7Slb0QYkcH
                    3Gr4+9qVn0lB1X10l9CsdN0K4goWbo/T8zEOQbjY9TVDI/IV4Q0x1gy74bFs5Pwer9DsxoEkg6qgLbERlLLXpq/f2QI2Mf
                    9G+SveshexswQwhtB6z8Uk27l4a2+/gv0rFrTdJrf9f+tMiwclvX9qhk9MnGnqVsfxFdNwRCfKyOnHJacBr3FQgMNcvs7c
                    DSTGxWAAZwUAvMXqyul/jBAklvryvwF42vGm8=
                    PIECES),
            ],
            $torrent->getRawData()['info']
        );
        self::assertEquals(380, \strlen($torrent->getRawData()['info']['pieces'])); // 19 chunks
        self::assertEquals('files', $torrent->getDisplayName());
        self::assertEquals('files.torrent', $torrent->getFileName());

        self::assertEquals(
            build_magnet_link([
                'xt=urn:btih:8d7b1593175abfa6563f7c8de082e5c46b3d1292',
                'dn=files',
            ]),
            $torrent->getMagnetLink()
        );
    }

    public function testNotFoundException(): void
    {
        $this->expectException(PathNotFoundException::class);
        TorrentFile::fromPath(TEST_ROOT . '/data/files/nosuchfile.txt');
    }

    public function testChunkTooSmall(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('pieceLength must be a power of 2 and at least 16384');

        TorrentFile::fromPath(
            TEST_ROOT . '/data/files/file1.txt',
            version: MetaVersion::V1,
            pieceLength: 1024,
        );
    }

    public function testChunkNotPow2(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('pieceLength must be a power of 2 and at least 16384');

        TorrentFile::fromPath(
            TEST_ROOT . '/data/files/file1.txt',
            version: MetaVersion::V1,
            pieceLength: 1024 * 1024 - 1,
        );
    }
}
