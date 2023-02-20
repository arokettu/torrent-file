<?php

declare(strict_types=1);

namespace Arokettu\Torrent;

enum MetaVersion: int
{
    case V1 = 1;
    case V2 = 2;

    public const HYBRID_V1V2 = [
        self::V1,
        self::V2,
    ];
    // It was an enum case
    // phpcs:ignore Generic.NamingConventions.UpperCaseConstantName.ClassConstantNotUpperCase
    public const HybridV1V2 = self::HYBRID_V1V2;
}
