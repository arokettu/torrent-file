<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests\Files;

use PHPUnit\Framework\TestCase;
use SandFox\Torrent\TorrentFile;

use const SandFox\Torrent\Tests\TEST_ROOT;

class CreateFileFeaturesTest extends TestCase
{
    public function testFiles2(): void
    {
        // test info dict on default settings
        $torrent = TorrentFile::fromPath(TEST_ROOT . '/data/files2');
        $info = $torrent->getRawData()['info'];

//        echo export_test_data($info);
        $this->assertEquals('be5a07b2ba3beb89e4b04132eb135e3f1d771bf6', $torrent->getInfoHash());
        $this->assertEquals(
            [
                'files' => [
                    [
                        'length' => 6621359,
                        'path' => ['dir1', 'file1.txt',],
                        'sha1' => base64_decode("FLpF01Q+gHDBdrRmIDPqQmKaYgQ="),
                    ],
                    [
                        'length' => 6621359,
                        'path' => ['dir2', 'file1.txt'],
                        'sha1' => base64_decode("FLpF01Q+gHDBdrRmIDPqQmKaYgQ="),
                    ],
                    [
                        'length' => 6621341,
                        'path' => ['dir2', 'file2.txt'],
                        'sha1' => base64_decode("JToK2HdRS+5VKZCu8WhvbV9a9KY="),
                    ],
                    [
                        'length' => 6621341,
                        'path' => ['dir3', 'file2.txt'],
                        'sha1' => base64_decode("JToK2HdRS+5VKZCu8WhvbV9a9KY="),
                    ],
                    [
                        'length' => 6291456,
                        'path' => ['dir4', 'aligned.txt'],
                        'sha1' => base64_decode("8uHdutKp152UxbBUEr66/UNo/I0="),
                    ],
                    [
                        'length' => 6621335,
                        'path' => ['dir5', 'file3.txt'],
                        'sha1' => base64_decode("WW5Dv31hzse3rO95vQfVTk7M3lg="),
                    ],
                    [
                        'attr' => 'x',
                        'length' => 6621355,
                        'path' => ['dir6', 'exec.txt'],
                        'sha1' => base64_decode("PLesPfBgCmcfBdyu9k95eUh8sfs="),
                    ],
                ],
                'name' => 'files2',
                'piece length' => 524288,
                'pieces' => base64_decode(<<<PIECES
                    UA6+qBSqwP7uJvTrqHs5iSp5mUcYJfIZ0wAyzY2UHsZoDGPTMYeNeHBiUmrKwus8K15+gprxhB4ZmcoA/4vOAEQncUHAAkG
                    2ApyqUloDAZ8XO3ktOMTUiQudWYbF+C7vrrYcJZZSA1ah8mNroUK9GEhJ/3tU40U4gfAgqRjk+AYay689QDM/8hpiYYegLm
                    NYntD0erSEXD7G9Fy4DT1SOMM4lHtUQsC+7erlN+apGisf4erLaK2bGTgKsbDwETNk115guP75OsxO499nbjEf7uzNnu+SV
                    o3wmeoI5/mx1jV2iihYK4Ow/iJL7yq2CUruWQcFLtKsJKHaFxXK+CHAjzDnmGafRpFsB4bSUzgp+0sQDXMUsrJSQLw/NDTm
                    Pn/bTnmGQHeBTgKmOmfD2xAzRH44urq87cHGS3rTl+0kX9B454fj+djA0Ql4IwaPDK1KLN86ZRCx3nK8SN3z1d1UDPUNbYc
                    3lmTP56S91AY8zPVy3fiYmlJg8SqEu8RX1Fj6Eui5l1sUq0hWI4yWpQAb2mKbQum/3eKi8GFijMbhHi6ohVt3ZxNuGaAn5R
                    8wJfygMlu2WAH/UyxBm2ImLUPmg5ymUyOM+iyq7hemxZabEhF6kr3W5zBwWhh4/eAn/gECBGIajasb9ifD0RGTtdrdDgQb6
                    lYawAJwPVnYXAfqhNdf0ZDGu/V4zJvykNJE0mLYJXruorPkMXTwmWGhT45YE5zVrslaF3ayAkaSqXFQFCgTwYVdA7oHBgch
                    B2mOo6K2XyV/Ja8HuQuhjjHSrp2rX61IJ8RQk9YyRld+9DCw2SLiRQ7IqV99lL2cBrVNJpr5r6/mr4M9WT1RHCy4Ka2jjEt
                    +DXtJIyZjwBmXv/nlL1/0PyRtYXVhvq3ZTAp/gt56UQ7pQBASePaQf5ZplVC9dO5OSmwXk18AuV0vZB+615jqxF56fAob9Q
                    7VWC7jt0AIhJtR/N59DLAgOTOTnzHiMQ4dFzlLSocSeoh+gi5NXrWn4g5OcYdukpL6A8pfqMDwewYZul01KJ0Lr4P/c1Pzy
                    3ZjOfd5ISYhIn2/3Of5MSd/j1rsz/bidGUixzHuqTjk94YhQUfeFTsbcz36JHaKB4nrjS2qAnq+n7on4JbeFh4PWgY5weTf
                    SwqALrIDBBSFWTiDxGrwbOK9LPiYMcUo7TLWaCwl4e+OvRrCpW1bNp5QAHWQ4yS5F9P1iv140i3PAb4CjeAGdqqyZqbfUN2
                    b8O72vPd1t6pxPqmcxQZ1tHdiZN01iGSHwTCk/gyJcfPh9ELQtnN6BYwC1POCGTTQ4qUh9V0w9fv4zUKxVYB6RLGJzlzUbB
                    vrcXxXePelOQfm0YNe35o2jqNZ/CwzkSFM3VAdMOo0dcM0BIujq/jBa5F677uVXvj5rrS0gyPgd1yv8vkDEA9C5p/3RSKO+
                    2F2LECpL2X0dtKU5yGriXadsDdx0Zkr7f3zgzPYnsJYIPJD5RGYrWOtxWWIiDdMU7SvHZKK+KiAfaPIQL/anaJdmsSd3NaT
                    nAu0/VrRX9+NB3JsldxvwBW2f4AlCOkO3mVB20yYFZi1QZSSAldR8PGTu5H3ScPeJGCMp9pRzaVRrtQsPlxv/x6SgX3PbSB
                    2a6FPUjg3smM7lfqqlOOA4XPG/hJPxvKO+MYWeVu9zoMt0TYPxYmMEwF9AAbtxGBiZmen+AiPqxIKbqPLlGSK1kZQ4oj6DN
                    NETQvJ6BWruBA7jp8CGRmEOQYOHn2fKrfMFleGdkEXbM6sEvS6GMHVP9NO4vRRm8vPnOry+zHnTumbuVCJaOHbSqGDziCH0
                    f/gZwP8iE/ioGfdMdvGpNh6wE3SBXENYRkEObnyzNCR8OYwej3jVwrA1vJquZNgGl24g7mcSeWx3CVDJQoBOdvL2uNv5k91
                    2NSqDsq148NC46Gg+bV9/FCzQxfgJkt34kRAgGWnIDgLRF1Tc34TIBE3N6gB6Ul3eTFgk/s25PSRFAYPD467EWXs/KggFeX
                    MHHDztpLCpOn9OvXzvrpsXHqAWXpFVyW91qVYtekTZKVe9LcaP9D0tAJjfzeATAcSZnl/w6dNq6dIKE0gfDg1++opAOwoUy
                    09Ivl0OrMK0zakrZIHjxD1D7XZyFquEtyoFncBAGoiemHgbjlbptBv2C0llg8SL6bgNsa2eTsB0Poa6Qdf0HTQA9Pxg+8E4
                    FnBBKIbmiq3sre29Cgg4BTOLCiv4a22cQgrwIXLHOHXVzwHj6flpCJMEZcT9zEEnIupHqMickRLbF27aB/qeAf1VLkNtHO0
                    SGuLrNX7IOfcMFBsSOmcEzxO4Qa1kc+f9y42rkssQgYWt54AgXGlnOMBZvmWVRn16K0=
                    PIECES),
            ],
            $info
        );
    }

    public function testExecutable(): void
    {
        $torrent = TorrentFile::fromPath(TEST_ROOT . '/data/files2', [
            'detectExec' => true,
        ]);

        $info = $torrent->getRawData()['info'];

        $xfile = [];
        foreach ($info['files'] as $file) {
            if ($file['path'] === ['dir6', 'exec.txt']) {
                $xfile = $file;
            }
        }

        self::assertStringContainsString('x', $xfile['attr']);
    }

    public function testSymlinks(): void
    {
        $torrent = TorrentFile::fromPath(TEST_ROOT . '/data/files2', [
            'detectSymlinks' => true,
        ]);

        $info = $torrent->getRawData()['info'];

//        echo export_test_data($info);
        $this->assertEquals(
            [
                'files' => [
                    [
                        // not link!
                        'length' => 6621359,
                        'path' => ['dir1', 'file1.txt'],
                        'sha1' => base64_decode("FLpF01Q+gHDBdrRmIDPqQmKaYgQ="),
                    ],
                    [
                        // link
                        'attr' => 'l',
                        'length' => 0,
                        'path' => ['dir2', 'file1.txt'],
                        'symlink path' => ['dir1', 'file1.txt'],
                    ],
                    [
                        // link
                        'attr' => 'l',
                        'length' => 0,
                        'path' => ['dir2', 'file2.txt'],
                        'symlink path' => ['dir3', 'file2.txt'],
                    ],
                    [
                        // not link!
                        'length' => 6621341,
                        'path' => ['dir3', 'file2.txt'],
                        'sha1' => base64_decode("JToK2HdRS+5VKZCu8WhvbV9a9KY="),
                    ],
                    [
                        'length' => 6291456,
                        'path' => ['dir4', 'aligned.txt'],
                        'sha1' => base64_decode("8uHdutKp152UxbBUEr66/UNo/I0="),
                    ],
                    [
                        // not link!
                        'length' => 6621335,
                        'path' => ['dir5', 'file3.txt'],
                        'sha1' => base64_decode("WW5Dv31hzse3rO95vQfVTk7M3lg="),
                    ],
                    [
                        'attr' => 'x',
                        'length' => 6621355,
                        'path' => ['dir6', 'exec.txt'],
                        'sha1' => base64_decode("PLesPfBgCmcfBdyu9k95eUh8sfs="),
                    ],
                ],
                'name' => 'files2',
                'piece length' => 524288,
                'pieces' => base64_decode(<<<PIECES
                    UA6+qBSqwP7uJvTrqHs5iSp5mUcYJfIZ0wAyzY2UHsZoDGPTMYeNeHBiUmrKwus8K15+gprxhB4ZmcoA/4vOAEQncUHAA
                    kG2ApyqUloDAZ8XO3ktOMTUiQudWYbF+C7vrrYcJZZSA1ah8mNroUK9GEhJ/3tU40U4gfAgqRjk+AYay689QDM/8hpiYY
                    egLmNYntD0erSEXD7G9Fy4DT1SOMM4lHtUQsC+7erlN+apGisf4erLaK2bGTgKsbDwETNk115guP75OsxO499nbjEf7uz
                    Nnu+SVo3wmeoI5/mx1jV2iihYK4Ow/iJL7yq2CUruihk4M/lebvPJnEfBTUvdCoFQgS9jQ2WiZD5/gYl0SBcWwh9FtIKZ
                    Ak4YSRTGIeRQ3uXrskTU0He9RBdgQaOZ8ABWE4SMUURL+XDW70fjiUa1tu8UBF8CXXhn79MxF6KXE582EpS4vihoyDRFG
                    Dm/LmkZzDsXFlSbSLDLkJQcQUKYcAiHDPhw/x3cDE4nFCeXrDD5KqFoSU00GjSJhZnOIWGP/217S/WL3BdekItM3HLx2B
                    q/QQUHmTimqOmC1s5vTkQBM1hhc+zts7/oBz5+Kz+9EDdgyjBCKs/x/fYk74HyKKwBz97lPpf+c+WUc+GkJMQO1S/BEdp
                    L4G3wghtWdDVw9alE62SqWu/GRbcLrrIlgjBaK0bcPbXFpcFU3qEUR0Pxv1hjwLr5uvskGDGwmwPuk8DqAfvL8hvCRJCV
                    OLE0HDN1KPOBFi7ss67AWYaM0xFy66QjMzaZqujBtRTzzrYchSH+MYDCA9uaDmzha3LfYUzsAScdz5S8nSpze+lEbPnU0
                    KiaVr+7jLNEWO+iV/7ZixtD2zvQ17AyG9Gj5wQ7EKcWKf+O+elKyeFKD5eBX23nEhk1XO4cwrJA1O5O1EuzEdqbFW/fXn
                    kMUDhJSdF1Xaqu3kf5HVx3VO840F0KrsS1qUTItSsK6YrTGLPIEPBDUSd9COYobuEtSCZOp8FyRBPaoK9C+7u6DrBeDQY
                    oc95Fwp8U3G/zEkOyapShH77K83cwoRUlir7GErzIwMQ6tBHnucv+xNbuZzEh30maSnRgk96BM7d+KYbwbisqJsTeQ+Zh
                    A8oYwi+/2i0Cwd8aiIXZpWVvciVjl+cImI+3YerfVB/wbTvWkfplvnorW/jk0kJRLWBuvJe1eqtqTPOyTYM+oYceqWoWU
                    sC+GaLXgNJ1x4lif2YAZ6jsTPn1DWAAk1jRaSop4JWdmzrwa89eMEutd/O0iLX3I5LKK/vm4uRtJPu835z3zR1JrRuzW3
                    J6TEE+yvVmYuZXhf1gffmMfEfA2VWYIejjFOl32f98K5AWf5MFeQak2SuX0XVbFsDff67LFumUQqxFepoaP+vFgnKhRxz
                    57+sGLMnJUEWGHDpxKQ65SKR01/trU7PsQcSc4QS35cCRBHtqakHMPjbhQKX/bONJHPKERYZfoMXW1VDcI00w2PyZlABc
                    WWpkmvpTNdynmRpa5GAuxpd6MnFA7jPUJH3TKdkL8lMuDoyoqj9Ts3Gm0uklbpkOoBXxLizJSByOt3/OWqs/8E8nWSkcv
                    Rcu6qW78hS32cr6P+Jk/tRi0wWc6twxlKnBTptaHWEl6d4WEj8mqJxELWyvIc7K5D6sM6fLUheOiYxUzl9k9ZSJIpIFmQ
                    rCx1NK
                    PIECES),
            ],
            $info
        );
    }

    public function testPadAlways(): void
    {
        $torrent = TorrentFile::fromPath(TEST_ROOT . '/data/files2', [
            'pieceAlign' => true,
        ]);

        $info = $torrent->getRawData()['info'];

//        echo export_test_data($info);
        $this->assertEquals(
            [
                'files' => [
                    [
                        'length' => 6621359,
                        'path' => ['dir1', 'file1.txt'],
                        'sha1' => base64_decode("FLpF01Q+gHDBdrRmIDPqQmKaYgQ="),
                    ],
                    [
                        'attr' => 'p',
                        'length' => 194385,
                        'path' => ['.pad', '194385'],
                    ],
                    [
                        'length' => 6621359,
                        'path' => ['dir2', 'file1.txt'],
                        'sha1' => base64_decode("FLpF01Q+gHDBdrRmIDPqQmKaYgQ="),
                    ],
                    [
                        'attr' => 'p',
                        'length' => 194385,
                        'path' => ['.pad', '194385'],
                    ],
                    [
                        'length' => 6621341,
                        'path' => ['dir2', 'file2.txt'],
                        'sha1' => base64_decode("JToK2HdRS+5VKZCu8WhvbV9a9KY="),
                    ],
                    [
                        'attr' => 'p',
                        'length' => 194403,
                        'path' => ['.pad', '194403'],
                    ],
                    [
                        'length' => 6621341,
                        'path' => ['dir3', 'file2.txt'],
                        'sha1' => base64_decode("JToK2HdRS+5VKZCu8WhvbV9a9KY="),
                    ],
                    [
                        'attr' => 'p',
                        'length' => 194403,
                        'path' => ['.pad', '194403'],
                    ],
                    [
                        // no padding after when file is already aligned
                        'length' => 6291456,
                        'path' => ['dir4', 'aligned.txt'],
                        'sha1' => base64_decode("8uHdutKp152UxbBUEr66/UNo/I0="),
                    ],
                    [
                        'length' => 6621335,
                        'path' => ['dir5', 'file3.txt'],
                        'sha1' => base64_decode("WW5Dv31hzse3rO95vQfVTk7M3lg="),
                    ],
                    [
                        'attr' => 'p',
                        'length' => 194409,
                        'path' => ['.pad', '194409'],
                    ],
                    [
                        'attr' => 'x',
                        'length' => 6621355,
                        'path' => ['dir6', 'exec.txt'],
                        'sha1' => base64_decode("PLesPfBgCmcfBdyu9k95eUh8sfs="),
                    ],
                ],
                'name' => 'files2',
                'piece length' => 524288,
                'pieces' => base64_decode(<<<PIECES
                    UA6+qBSqwP7uJvTrqHs5iSp5mUcYJfIZ0wAyzY2UHsZoDGPTMYeNeHBiUmrKwus8K15+gprxhB4ZmcoA/4vOAEQncUHA
                    AkG2ApyqUloDAZ8XO3ktOMTUiQudWYbF+C7vrrYcJZZSA1ah8mNroUK9GEhJ/3tU40U4gfAgqRjk+AYay689QDM/8hpi
                    YYegLmNYntD0erSEXD7G9Fy4DT1SOMM4lHtUQsC+7erlN+apGisf4erLaK2bGTgKsbDwETNk115guP75OsxO499nbjEf
                    7uzNnu+SVo3wmeoI5/mx1jV2iihYK4Ow/iJL7yq2CUruA02xqlYBnQZYFR1EwRjpGzgLuklQDr6oFKrA/u4m9OuoezmJ
                    KnmZRxgl8hnTADLNjZQexmgMY9Mxh414cGJSasrC6zwrXn6CmvGEHhmZygD/i84ARCdxQcACQbYCnKpSWgMBnxc7eS04
                    xNSJC51ZhsX4Lu+uthwlllIDVqHyY2uhQr0YSEn/e1TjRTiB8CCpGOT4BhrLrz1AMz/yGmJhh6AuY1ie0PR6tIRcPsb0
                    XLgNPVI4wziUe1RCwL7t6uU35qkaKx/h6storZsZOAqxsPARM2TXXmC4/vk6zE7j32duMR/u7M2e75JWjfCZ6gjn+bHW
                    NXaKKFgrg7D+IkvvKrYJSu4DTbGqVgGdBlgVHUTBGOkbOAu6SfGd0UCYcHcCRmfE6oWkmfuP3hUQkT3cotC/ypkngzAL
                    aNb8tSBh8o4s30NBRUecdo/mdf+aBwR/7smj594UsZf4Hk209atGipKi7k9fjkxPhHdwQQTyCA5ruZRqMKZLJmRkBn8M
                    kDIJ3LiiGoANq2czAUh6kIttqJie+Hz5pcbDVpqoORTgucqpEebMODMSep65T5SEdxE6O7ICa3l8d0i1o/RjYorSVlTa
                    RKfpUtP7IzI41Ql+lKty9nCoOz/RRKBaCamz7UbiXo7hSR8dCXlzdcNgfCBTVNZxdGwC+jsRFiZEbG5RCtVQXaLHXtb1
                    AzRqk1+1gJoaQIn6bDvC2shabg4u8Z3RQJhwdwJGZ8TqhaSZ+4/eFRCRPdyi0L/KmSeDMAto1vy1IGHyjizfQ0FFR5x2
                    j+Z1/5oHBH/uyaPn3hSxl/geTbT1q0aKkqLuT1+OTE+Ed3BBBPIIDmu5lGowpksmZGQGfwyQMgncuKIagA2rZzMBSHqQ
                    i22omJ74fPmlxsNWmqg5FOC5yqkR5sw4MxJ6nrlPlIR3ETo7sgJreXx3SLWj9GNiitJWVNpEp+lS0/sjMjjVCX6Uq3L2
                    cKg7P9FEoFoJqbPtRuJejuFJHx0JeXN1w2B8IFNU1nF0bAL6OxEWJkRsblEK1VBdosde1vUDNGqTX7WAmhpAifpsO8La
                    yFpuDi5QDr6oFKrA/u4m9OuoezmJKnmZRxgl8hnTADLNjZQexmgMY9Mxh414cGJSasrC6zwrXn6CmvGEHhmZygD/i84A
                    RCdxQcACQbYCnKpSWgMBnxc7eS04xNSJC51ZhsX4Lu+uthwlllIDVqHyY2uhQr0YSEn/e1TjRTiB8CCpGOT4BhrLrz1A
                    Mz/yGmJhh6AuY1ie0PR6tIRcPsb0XLgNPVI4wziUe1RCwL7t6uU35qkaKx/h6storZsZOAqxsPARM2TXXmC4/vk6zE7j
                    32duMR/u7M2e75JWjfCZ6gjn+bHWNXaKKFgrg7D+IkvvKrYJSu4o168liUzcR+xQkoQ+olUhU/wil1OrvHgj4Zx9Ygn6
                    hdmmEdmBHVDxNfXgWs1CeCfECxNyINTe6ObshGAsJ1hIxUunzL9Sz2dPh6Lkb3Z5MI1mYuKf8WaMN3FAV1Yk9Gm9xmUf
                    BuhItNqkf7Tl/z6n7laSudBf58mUZIFAHWRF0LwNWIX4lWaJtnHXY6JuTmRbEuxonF4ZkhoAozNAVoSqGW6eCj5Z7SJC
                    itr+opamt7ZqMgU9nYoB0kx+5muwh0KyJDjmW0QqpsH+mZfDXwV0G5NyX7mnykr7Qw8Z2kb3Lb9OSibXFTjasGZK2akK
                    JfFbCYZ0/DeekY26/ByqqNs8zRvkyE63QZojMIaSG2VcbzpPzbusLvpeojitOr2+/5su6UKaDqAyuD4FKE/pBrC/Ehk0
                    UxjrU2mseOIy0egWBeDoj2kFSs/Op5EpAB8wlsk85T+xyvfA2LKjBvTScIKsSci+tlWvlOh6sDG/siLyrXWqodoBW0Xo
                    4kv3wqRW86o3s5cbuRFM2JRi9t3Uk+q+sfJ8e9fmTl+x0Ycc0tm1brX8Y8bwmzYEfsg7qgAWau65ny/KtLrou4LaHIIk
                    vH1gMVHrr2SN0C3PU90u/2g7vcwEcbVoloegxCRdcD+Bc8ERdWw5e8++0smCHqZ/7RP+67paBbIi6WBLebfa4EWqU8/T
                    6Eihnq8O
                    PIECES),
            ],
            $info
        );
    }
}
