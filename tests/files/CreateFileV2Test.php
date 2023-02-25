<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests\Files;

use Arokettu\Torrent\MetaVersion;
use Arokettu\Torrent\TorrentFile;
use PHPUnit\Framework\TestCase;

use function Arokettu\Torrent\Tests\build_magnet_link;
use function Arokettu\Torrent\Tests\raw_torrent_data;

use const Arokettu\Torrent\Tests\TEST_ROOT;

class CreateFileV2Test extends TestCase
{
    public function testSingleFile(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/files/file1.txt',
            version: MetaVersion::V2,
        ); // approx 6 mb
        $torrent->setCreationDate(null); // always changes

        self::assertNull($torrent->v1());
        self::assertEquals(
            '9744050ac753ffb072da78ae4c804c52fafa1943c17ac045dd1e794a3a86018f',
            $torrent->v2()->getInfoHash()
        );
//        echo export_test_data($torrent->getRawData());
        self::assertEquals([
            'created by' => 'Torrent File by Sand Fox https://sandfox.dev/php/torrent-file.html',
            'info' => [
                'file tree' => [
                    'file1.txt' => [
                        '' => [
                            'length' => 6621359,
                            'pieces root' => base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4="),
                        ],
                    ],
                ],
                'meta version' => 2,
                'name' => 'file1.txt',
                'piece length' => 524288,
            ],
            'piece layers' => [
                base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4=") => base64_decode(<<<LAYER
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
        self::assertFalse($torrent->isDirectory());

        self::assertEquals(
            build_magnet_link([
                'xt=urn:btmh:12209744050ac753ffb072da78ae4c804c52fafa1943c17ac045dd1e794a3a86018f',
                'dn=file1.txt',
            ]),
            $torrent->getMagnetLink()
        );
    }

    public function testSingleFile16KB(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/files/file1.txt',
            version: MetaVersion::V2,
            pieceLength: 16384,
        ); // approx 6 mb
        $torrent->setCreationDate(null); // always changes

        $raw = raw_torrent_data($torrent);
        $raw['piece layers'] = array_map(fn ($s) => \strlen($s), $raw['piece layers']); // very long with these hashes

        self::assertEquals(
            '684bdfc6d44d85e55f6cf292efd2349273d3bab5cadb951fd38102bdc0a45c06',
            $torrent->v2()->getInfoHash()
        );
//        echo export_test_data($torrent->getRawData());
        self::assertEquals([
            'created by' => 'Torrent File by Sand Fox https://sandfox.dev/php/torrent-file.html',
            'info' => [
                'file tree' => [
                    'file1.txt' => [
                        '' => [
                            'length' => 6621359,
                            'pieces root' => base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4="),
                        ],
                    ],
                ],
                'meta version' => 2,
                'name' => 'file1.txt',
                'piece length' => 16384,
            ],
            'piece layers' => [
                base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4=") => 12960, // was very long
            ],
        ], $raw);
        self::assertEquals('file1.txt', $torrent->getDisplayName());
        self::assertEquals('file1.txt.torrent', $torrent->getFileName());
        self::assertFalse($torrent->isDirectory());

        self::assertEquals(
            build_magnet_link([
                'xt=urn:btmh:1220684bdfc6d44d85e55f6cf292efd2349273d3bab5cadb951fd38102bdc0a45c06',
                'dn=file1.txt',
            ]),
            $torrent->getMagnetLink()
        );
    }

    public function testMultipleFiles(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/files',
            version: MetaVersion::V2,
        ); // approx 19 mb
        $torrent->setCreationDate(null); // always changes

        self::assertEquals(
            'ed751104df9a3d16a141aea0e86cc03b0a5d591f18ee0f70162e68ec8c218f97',
            $torrent->v2()->getInfoHash(),
        );
//        echo export_test_data($torrent->getRawData());
        self::assertEquals(
            [
                'created by' => 'Torrent File by Sand Fox https://sandfox.dev/php/torrent-file.html',
                'info' => [
                    'file tree' => [
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
                        'file3.txt' => [
                            '' => [
                                'length' => 6621335,
                                'pieces root' => base64_decode("5LnSj1BlgMSXaD9sLMbo8odfsnlSx5WVV1KOirR3zPk="),
                            ],
                        ],
                    ],
                    'meta version' => 2,
                    'name' => 'files',
                    'piece length' => 524288,
                ],
                'piece layers' => [
                    base64_decode("W64kU2QHo/iMSgYP6thVUL0nPGqyH4/iZcrYEonjIyk=") => base64_decode(<<<LAYER
                        9KpjHwtv1cPE0lXZ772SBAf1uaJCh8rlhXIZG1drJIOH3HNdb5FbTsB8waV4XK/JDdVQziyqj9RPKesx3JdRRHxJDI
                        j989oNALmeiXmHxEhG2gwNNUEXXgg1+CVmEHNofIUG77UVZmqEmx+ZGqjBgBbL1qxcucxNcJWnQqffU1pNeGvZMnNs
                        GhzPCiPDQ7IvAEJW1bYz7At4Jy3aEhTZsXl8prY1Jjg88rTunwd6IRz9+3ms3EQ+D/QTA2XYOEML2eXZAY7ApWSUrE
                        scIlcLyptT8s/c9A9e27QIPbvsDHSKWJSbdf+IvTn32+jdbLoVlS65XZA5qiSf2DV7dyyVIVxeq9rNvvfVbtxpA/lH
                        rlE0j1CISHLq/Hv5x3CZrhgB6rjSvy/f1WU9hNvhOV+XHWWHiVQctWIrVP1H/VdV6xnjOy4nx+jMGGyUggvjgKC3Yi
                        18+85djD+fJsKB1VT1mMyYwHg3jPLZU4JRKg93EPD8AtGkKS9b+Fo6HprySbLvyJ2qyuupiPBqDM2okr6YH4jsCZNY
                        xPDmCGS0yJbkqgw=
                        LAYER),
                    base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4=") => base64_decode(<<<LAYER
                        pJ01IFJm8LryTFwzpd1vteR+/BZrLbmtybsOede13OMmZZosMg5qEWPdWv0RY3t90BC0F6vtjPgFurBi7UC8k2565k
                        vea7/DKrwBOJlBY3RrKcNofSNikmua350/ojuUrjIbw7zgzO0XreeImRK32QWl+WE/nBjyejAGCnH5XDHT+ImER/c3
                        t0vqyzVinCDg9xRjmwwwxBnZozCIeQAGeDeNigw8jPamcDIhTXsORZMuAM6UZT7Za7BsEgykAj3hk95FWYMaX444hs
                        74g7o2pwwcCg7YIySilMySUJ7KTfU1xGzcNIPwkHF6AUmNnfYzwZu36/WNOWgFlaGhkPcnopaO0wMEUm8BLVbrbrOr
                        zb/KzsOD5VOglmjWqq+yRc6aQQphxpJXZ6NslSxbYkeFBdeL5KGUGGUUebhyRnaK5mHar5PEEQAwUptef0Kxsgt/fe
                        Qz4shvJL702xjm+Pg9XjyEaYTIJ+GGshceSRkcWcDUEzdUMTos4c8sz3Gtywycv+Mn5U2mslfFOW3bIafnwo54feqx
                        +HKXFc6BjmfWtQo=
                        LAYER),
                    base64_decode("5LnSj1BlgMSXaD9sLMbo8odfsnlSx5WVV1KOirR3zPk=") => base64_decode(<<<LAYER
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
        self::assertTrue($torrent->isDirectory());

        self::assertEquals(
            build_magnet_link([
                'xt=urn:btmh:1220ed751104df9a3d16a141aea0e86cc03b0a5d591f18ee0f70162e68ec8c218f97',
                'dn=files',
            ]),
            $torrent->getMagnetLink()
        );
    }

    public function testMultipleFiles1MB(): void
    {
        $torrent = TorrentFile::fromPath(
            TEST_ROOT . '/data/files',
            version: MetaVersion::V2,
            pieceLength: 1024 * 1024, // 1mb chunk
        ); // approx 19 mb
        $torrent->setCreationDate(null); // always changes

        self::assertEquals(
            '81b558cd173dd0645bb243a8db9b326f1b2c3a8e952d0b6401bb64ed757919b0',
            $torrent->v2()->getInfoHash(),
        );
//        echo export_test_data($torrent->getRawData());
        self::assertEquals(
            [
                'created by' => 'Torrent File by Sand Fox https://sandfox.dev/php/torrent-file.html',
                'info' => [
                    'file tree' => [
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
                        'file3.txt' => [
                            '' => [
                                'length' => 6621335,
                                'pieces root' => base64_decode("5LnSj1BlgMSXaD9sLMbo8odfsnlSx5WVV1KOirR3zPk="),
                            ],
                        ],
                    ],
                    'meta version' => 2,
                    'name' => 'files',
                    'piece length' => 1048576,
                ],
                'piece layers' => [
                    base64_decode("W64kU2QHo/iMSgYP6thVUL0nPGqyH4/iZcrYEonjIyk=") => base64_decode(<<<LAYER
                        Jn8jIxH3ygU+8BT+LFMgfQaWyYfDVCLfU7H+dNfcf9Nvzgp0wWxlioZRQpdsXOZXTxS5MO8xB2UiLVNwzQ64P51W
                        oj5dM0ViXaG63oYBQ3p+IFNCjz8mM8znqOOFuZN2vnfMYnMfQf+6dk6ybPHULAfUFJGzwlOXEaZBVL5o4Ro54rn9
                        MiFXvFpT0QG5yT9LrB/CzdDiWKilPtwrj1ZLZhQi4hZ91dzcKfy7A8bSarWTQnAcRk8TjAMN+nPLKtFJbcLtsC2C
                        vVUFKWI4jaGDyyCcT1bKf1fDos8/jPkvqxA=
                        LAYER),
                    base64_decode("blnDZvWzIQDgU4E05AOY3k0fr92/qGjBpKHWM4osCn4=") => base64_decode(<<<LAYER
                        2yPr9iikbQHomNUZsdiopps7MvfiRc0F0r+7guEZzL0nJeRjHx1OUkkLXS8nKI22m8gJejdvP6VfwPTGFUI+Iktd
                        d6KkxlmijDdq/gK4ykRRAWF+gO2WqZ4A4Kg7QyrbWFBtyX1JGSEiyVRivsgfNp7ogEfuSCa1YRJvygoSxxWUBb1z
                        xTDyAYRvT7h2u0ocxL9hHl7oj47Em01HavaLou+NgZXqBN9NX7y9PCU7jemqi1wOpWE+/sEIF0araWjlFRMyUOYv
                        6QDv++yV4frbN1Q0yS5PN4un5zPED17RWj0=
                        LAYER),
                    base64_decode("5LnSj1BlgMSXaD9sLMbo8odfsnlSx5WVV1KOirR3zPk=") => base64_decode(<<<LAYER
                        44881sDIuCcvg296HSP/ccx6mTdH+ovWDxwV40a7NIoecCWcDsjWErE0dzBEzapUhbHPzNmZn3RzerNocCLV781i
                        O2fvGiHcz3aSo7E1ad0INCi7it1IqVAzbylQ2KVYhDuDdRV/SLBbr8brq/JsgdDIbJa7V70e+6iF7/xsMSrvxuCa
                        PVEA2fKvNk4/jnVPJ3nFkO7W7GuQ2/iye1eAGGrYxiwU4F9/Z8n12w9ZnOU/pMdW1TuAjZUOdDnLr/CrXpiid287
                        Vuj+kD11wIvloQsil3GiPlK+8lYAD1IGsjw=
                        LAYER),
                ],
            ],
            raw_torrent_data($torrent)
        );
        self::assertEquals('files', $torrent->getDisplayName());
        self::assertEquals('files.torrent', $torrent->getFileName());

        self::assertEquals(
            build_magnet_link([
                'xt=urn:btmh:122081b558cd173dd0645bb243a8db9b326f1b2c3a8e952d0b6401bb64ed757919b0',
                'dn=files',
            ]),
            $torrent->getMagnetLink()
        );
    }
}
