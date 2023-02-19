<?php

declare(strict_types=1);

namespace Arokettu\Torrent;

enum MetaVersion: int
{
    case V1 = 1;
    case V2 = 2;

    public const HybridV1V2 = [
        self::V1,
        self::V2,
    ];
}
