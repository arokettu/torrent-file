<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Files;

use Arokettu\Torrent\MetaVersion;
use Arokettu\Torrent\TorrentFile;
use PHPUnit\Framework\TestCase;

use function Arokettu\Torrent\Tests\build_magnet_link;
use function Arokettu\Torrent\Tests\raw_torrent_data;

use const Arokettu\Torrent\Tests\TEST_ROOT;

class CreateFileHybridTest extends TestCase
{
    public function testSingleFile(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/files/file1.txt',
            version: MetaVersion::HybridV1V2,
            creationDate: new \DateTimeImmutable('@' . 1_500_000_000),
        ); // approx 6 mb

        self::assertEquals(
            '598bc421858f96416ac17c399a48af67fa8ffc4c',
            $torrent->v1()->getInfoHash()
        );
        self::assertEquals(
            '79cce7c3c5adaa1dabe47cb808b0eabd8c0a9de2076533e9ee2e2cdec7819db6',
            $torrent->v2()->getInfoHash()
        );
//        echo export_test_data($torrent->getRawData());
        self::assertEquals([
            'created by' => 'Torrent File by Sand Fox https://sandfox.dev/php/torrent-file.html',
            'creation date' => 1_500_000_000,
            'info' => [
                'file tree' => [
                    'file1.txt' => [
                        '' => [
                            'length' => 6621359,
                            'pieces root' => base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                        ],
                    ],
                ],
                'files' => [[
                    'length' => 6621359,
                    'path' => ['file1.txt'],
                    'sha1' => base64_decode('FLpF01Q+gHDBdrRmIDPqQmKaYgQ='),
                ]],
                'meta version' => 2,
                'name' => 'file1.txt',
                'piece length' => 524288,
                'pieces' => base64_decode(<<<PIECES
                    UA6+qBSqwP7uJvTrqHs5iSp5mUcYJfIZ0wAyzY2UHsZoDGPTMYeNeHBiUmrKwus8K15+gprxhB4ZmcoA/4vOAEQnc
                    UHAAkG2ApyqUloDAZ8XO3ktOMTUiQudWYbF+C7vrrYcJZZSA1ah8mNroUK9GEhJ/3tU40U4gfAgqRjk+AYay689QD
                    M/8hpiYYegLmNYntD0erSEXD7G9Fy4DT1SOMM4lHtUQsC+7erlN+apGisf4erLaK2bGTgKsbDwETNk115guP75Osx
                    O499nbjEf7uzNnu+SVo3wmeoI5/mx1jV2iihYK4Ow/iJL7yq2CUruoTvVHnSPqq4c3I2T5nT3YPQqLBc=
                    PIECES),
            ],
            'piece layers' => [
                base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4=') => base64_decode(<<<LAYER
                    pJ01IFJm8LryTFwzpd1vteR+/BZrLbmtybsOede13OMmZZosMg5qEWPdWv0RY3t90BC0F6vtjPgFurBi7UC8k2565kvea
                    7/DKrwBOJlBY3RrKcNofSNikmua350/ojuUrjIbw7zgzO0XreeImRK32QWl+WE/nBjyejAGCnH5XDHT+ImER/c3t0vqyz
                    VinCDg9xRjmwwwxBnZozCIeQAGeDeNigw8jPamcDIhTXsORZMuAM6UZT7Za7BsEgykAj3hk95FWYMaX444hs74g7o2pww
                    cCg7YIySilMySUJ7KTfU1xGzcNIPwkHF6AUmNnfYzwZu36/WNOWgFlaGhkPcnopaO0wMEUm8BLVbrbrOrzb/KzsOD5VOg
                    lmjWqq+yRc6aQQphxpJXZ6NslSxbYkeFBdeL5KGUGGUUebhyRnaK5mHar5PEEQAwUptef0Kxsgt/feQz4shvJL702xjm+
                    Pg9XjyEaYTIJ+GGshceSRkcWcDUEzdUMTos4c8sz3Gtywycv+Mn5U2mslfFOW3bIafnwo54feqx+HKXFc6BjmfWtQo=
                    LAYER),
            ],
        ], raw_torrent_data($torrent));
        self::assertEquals('file1.txt', $torrent->getDisplayName());
        self::assertEquals('file1.txt.torrent', $torrent->getFileName());
        self::assertFalse($torrent->v1()->isDirectory());
        self::assertFalse($torrent->v2()->isDirectory());

        self::assertEquals(
            build_magnet_link([
                'xt=urn:btih:598bc421858f96416ac17c399a48af67fa8ffc4c',
                'xt=urn:btmh:122079cce7c3c5adaa1dabe47cb808b0eabd8c0a9de2076533e9ee2e2cdec7819db6',
                'dn=file1.txt',
            ]),
            $torrent->getMagnetLink()
        );
    }

    public function testMultipleFiles(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/files',
            version: MetaVersion::HybridV1V2,
            creationDate: new \DateTimeImmutable('@' . 1_500_000_000),
        ); // approx 19 mb

        self::assertEquals(
            '8223a88d2a642b1dfc58c7c6c2e9d43fcbbd0c32',
            $torrent->v1()->getInfoHash()
        );
        self::assertEquals(
            '3531223d5ac920e55fa32510253dfa2f5bcd9d2d836c482e65ca1aecbab0b9eb',
            $torrent->v2()->getInfoHash()
        );
//        echo export_test_data($torrent->getRawData());
        self::assertEquals(
            [
                'created by' => 'Torrent File by Sand Fox https://sandfox.dev/php/torrent-file.html',
                'creation date' => 1_500_000_000,
                'info' => [
                    'file tree' => [
                        'file1.txt' => [
                            '' => [
                                'length' => 6621359,
                                'pieces root' => base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4='),
                            ],
                        ],
                        'file2.txt' => [
                            '' => [
                                'length' => 6621341,
                                'pieces root' => base64_decode('W64kU2QHo/iMSgYP6thVUL0nPGqyH4/iZcrYEonjIyk='),
                            ],
                        ],
                        'file3.txt' => [
                            '' => [
                                'length' => 6621335,
                                'pieces root' => base64_decode('5LnSj1BlgMSXaD9sLMbo8odfsnlSx5WVV1KOirR3zPk='),
                            ],
                        ],
                    ],
                    'files' => [
                        [
                            'length' => 6621359,
                            'path' => ['file1.txt'],
                            'sha1' => base64_decode('FLpF01Q+gHDBdrRmIDPqQmKaYgQ='),
                        ],
                        [
                            'attr' => 'p',
                            'length' => 194385,
                            'path' => ['.pad', '194385'],
                        ],
                        [
                            'length' => 6621341,
                            'path' => ['file2.txt'],
                            'sha1' => base64_decode('JToK2HdRS+5VKZCu8WhvbV9a9KY='),
                        ],
                        [
                            'attr' => 'p',
                            'length' => 194403,
                            'path' => ['.pad', '194403'],
                        ],
                        [
                            'length' => 6621335,
                            'path' => ['file3.txt'],
                            'sha1' => base64_decode('WW5Dv31hzse3rO95vQfVTk7M3lg='),
                        ],
                    ],
                    'meta version' => 2,
                    'name' => 'files',
                    'piece length' => 524288,
                    'pieces' => base64_decode(<<<PIECES
                        UA6+qBSqwP7uJvTrqHs5iSp5mUcYJfIZ0wAyzY2UHsZoDGPTMYeNeHBiUmrKwus8K15+gprxhB4ZmcoA/4vOAEQncU
                        HAAkG2ApyqUloDAZ8XO3ktOMTUiQudWYbF+C7vrrYcJZZSA1ah8mNroUK9GEhJ/3tU40U4gfAgqRjk+AYay689QDM/
                        8hpiYYegLmNYntD0erSEXD7G9Fy4DT1SOMM4lHtUQsC+7erlN+apGisf4erLaK2bGTgKsbDwETNk115guP75OsxO49
                        9nbjEf7uzNnu+SVo3wmeoI5/mx1jV2iihYK4Ow/iJL7yq2CUruA02xqlYBnQZYFR1EwRjpGzgLuknxndFAmHB3AkZn
                        xOqFpJn7j94VEJE93KLQv8qZJ4MwC2jW/LUgYfKOLN9DQUVHnHaP5nX/mgcEf+7Jo+feFLGX+B5NtPWrRoqSou5PX4
                        5MT4R3cEEE8ggOa7mUajCmSyZkZAZ/DJAyCdy4ohqADatnMwFIepCLbaiYnvh8+aXGw1aaqDkU4LnKqRHmzDgzEnqe
                        uU+UhHcROjuyAmt5fHdItaP0Y2KK0lZU2kSn6VLT+yMyONUJfpSrcvZwqDs/0USgWgmps+1G4l6O4UkfHQl5c3XDYH
                        wgU1TWcXRsAvo7ERYmRGxuUQrVUF2ix17W9QM0apNftYCaGkCJ+mw7wtrIWm4OLijXryWJTNxH7FCShD6iVSFT/CKX
                        U6u8eCPhnH1iCfqF2aYR2YEdUPE19eBazUJ4J8QLE3Ig1N7o5uyEYCwnWEjFS6fMv1LPZ0+HouRvdnkwjWZi4p/xZo
                        w3cUBXViT0ab3GZR8G6Ei02qR/tOX/PqfuVpK50F/nyZRkgUAdZEXQvA1YhfiVZom2cddjom5OZFsS7GicXhmSGgCj
                        M0BWhKoZbp4KPlntIkKK2v6ilqa3tmoyBT2digHSTH7ma7CHQrIkOOZbRCqmwf6Zl8NfBXQbk3JfuafKSvtDDxnaRv
                        ctv05KJtcVONqwZkrZqQol8S9Yp2VHn8cdQCJbE147XOheodLp
                        PIECES),
                ],
                'piece layers' => [
                    base64_decode('W64kU2QHo/iMSgYP6thVUL0nPGqyH4/iZcrYEonjIyk=') => base64_decode(<<<LAYER
                        9KpjHwtv1cPE0lXZ772SBAf1uaJCh8rlhXIZG1drJIOH3HNdb5FbTsB8waV4XK/JDdVQziyqj9RPKesx3JdRRHxJDI
                        j989oNALmeiXmHxEhG2gwNNUEXXgg1+CVmEHNofIUG77UVZmqEmx+ZGqjBgBbL1qxcucxNcJWnQqffU1pNeGvZMnNs
                        GhzPCiPDQ7IvAEJW1bYz7At4Jy3aEhTZsXl8prY1Jjg88rTunwd6IRz9+3ms3EQ+D/QTA2XYOEML2eXZAY7ApWSUrE
                        scIlcLyptT8s/c9A9e27QIPbvsDHSKWJSbdf+IvTn32+jdbLoVlS65XZA5qiSf2DV7dyyVIVxeq9rNvvfVbtxpA/lH
                        rlE0j1CISHLq/Hv5x3CZrhgB6rjSvy/f1WU9hNvhOV+XHWWHiVQctWIrVP1H/VdV6xnjOy4nx+jMGGyUggvjgKC3Yi
                        18+85djD+fJsKB1VT1mMyYwHg3jPLZU4JRKg93EPD8AtGkKS9b+Fo6HprySbLvyJ2qyuupiPBqDM2okr6YH4jsCZNY
                        xPDmCGS0yJbkqgw=
                        LAYER),
                    base64_decode('blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4=') => base64_decode(<<<LAYER
                        pJ01IFJm8LryTFwzpd1vteR+/BZrLbmtybsOede13OMmZZosMg5qEWPdWv0RY3t90BC0F6vtjPgFurBi7UC8k2565k
                        vea7/DKrwBOJlBY3RrKcNofSNikmua350/ojuUrjIbw7zgzO0XreeImRK32QWl+WE/nBjyejAGCnH5XDHT+ImER/c3
                        t0vqyzVinCDg9xRjmwwwxBnZozCIeQAGeDeNigw8jPamcDIhTXsORZMuAM6UZT7Za7BsEgykAj3hk95FWYMaX444hs
                        74g7o2pwwcCg7YIySilMySUJ7KTfU1xGzcNIPwkHF6AUmNnfYzwZu36/WNOWgFlaGhkPcnopaO0wMEUm8BLVbrbrOr
                        zb/KzsOD5VOglmjWqq+yRc6aQQphxpJXZ6NslSxbYkeFBdeL5KGUGGUUebhyRnaK5mHar5PEEQAwUptef0Kxsgt/fe
                        Qz4shvJL702xjm+Pg9XjyEaYTIJ+GGshceSRkcWcDUEzdUMTos4c8sz3Gtywycv+Mn5U2mslfFOW3bIafnwo54feqx
                        +HKXFc6BjmfWtQo=
                        LAYER),
                    base64_decode('5LnSj1BlgMSXaD9sLMbo8odfsnlSx5WVV1KOirR3zPk=') => base64_decode(<<<LAYER
                        rXI7o2a3/k6ycX5hVUAtaPX15DpQv/NlHaPSeucpH6Te8UcFtgGNZbPlbyyiS7HW00HWlymu9PGybG9B5uSA5lFa1y
                        BeiTvXVbBSG3g0GnaZ+7IxJraVpWCJaBJ0jQwZxGYOagNigd6mMbWp/iVuYRQJ8Tmp6oSHLu6NkTEmIB8WCYYEzheh
                        061HFk+4vuya5VxgvKVZH/SrHj9pnq8vdLSCma40jQWn1N1+R2udq1RqnOE47Ocdlclkf6/Ur15ZAeVsoyOSFtNTyt
                        oZ+MucUG92zD/kkQl/i7Yf1JQchtnjdfIPlKEqy4PsS65tRN7H04jVko19pDKNK7X3HvQMw8Ds++0mdfJqKqRJgA2c
                        VriooDgC3AIi1O+QNJZrWzpksgszxp02ay+KxLFcrDDuCUCiJT+VYdllxoAyM/bkJmPj2TtAq9hvSiPV6SR5xEcLmn
                        JVlfPMkFKVEocaqJEgtnN2Pu0o8VaEh2kbDkTkwuegm393S4vB1ztLHH8Aaeq96fkndb1RTxknWFkM6sjo2NRt3qG8
                        zwEmeiHeg3W1d9M=
                        LAYER),
                ],
            ],
            raw_torrent_data($torrent),
        );
        self::assertEquals('files', $torrent->getDisplayName());
        self::assertEquals('files.torrent', $torrent->getFileName());
        self::assertTrue($torrent->v1()->isDirectory());
        self::assertTrue($torrent->v2()->isDirectory());

        self::assertEquals(
            build_magnet_link([
                'xt=urn:btih:8223a88d2a642b1dfc58c7c6c2e9d43fcbbd0c32',
                'xt=urn:btmh:12203531223d5ac920e55fa32510253dfa2f5bcd9d2d836c482e65ca1aecbab0b9eb',
                'dn=files',
            ]),
            $torrent->getMagnetLink()
        );
    }

    public function testEmptyFile(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/empty_file.txt',
            version: MetaVersion::HybridV1V2,
            creationDate: new \DateTimeImmutable('@' . 1_500_000_000),
        ); // approx 6 mb

        self::assertEquals(
            '7d5393e3c1a4bf57fe254be62bbd6b900eb0d605',
            $torrent->v1()->getInfoHash()
        );
        self::assertEquals(
            '3d053755a83a4f287358f0a2d50880047202ab04c70dcf6e3786dccf53cd95c8',
            $torrent->v2()->getInfoHash()
        );
//        echo export_test_data($torrent->getRawData());
        self::assertEquals([
            'created by' => 'Torrent File by Sand Fox https://sandfox.dev/php/torrent-file.html',
            'creation date' => 1_500_000_000,
            'info' => [
                'file tree' => [
                    'empty_file.txt' => [
                        '' => [
                            'length' => 0,
                        ],
                    ],
                ],
                'files' => [[
                    'length' => 0,
                    'sha1' => base64_decode('2jmj7l5rSw0yVb/vlWAYkK/YBwk='),
                    'path' => ['empty_file.txt'],
                ]],
                'meta version' => 2,
                'name' => 'empty_file.txt',
                'piece length' => 524288,
                'pieces' => '',
            ],
            'piece layers' => [
            ],
        ], raw_torrent_data($torrent));
        self::assertEquals('empty_file.txt', $torrent->getDisplayName());
        self::assertEquals('empty_file.txt.torrent', $torrent->getFileName());
        self::assertFalse($torrent->v1()->isDirectory());
        self::assertFalse($torrent->v2()->isDirectory());

        self::assertEquals(
            build_magnet_link([
                'xt=urn:btih:7d5393e3c1a4bf57fe254be62bbd6b900eb0d605',
                'xt=urn:btmh:12203d053755a83a4f287358f0a2d50880047202ab04c70dcf6e3786dccf53cd95c8',
                'dn=empty_file.txt',
            ]),
            $torrent->getMagnetLink()
        );
    }
}
