<?php

declare(strict_types=1);

namespace Arokettu\Torrent;

enum MetaVersion
{
    case V1;
    case V2;
    case HybridV1V2;
}
