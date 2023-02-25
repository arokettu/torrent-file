<?php

declare(strict_types=1);

namespace Arokettu\Torrent\V1;

use Arokettu\Torrent\DataTypes\Internal\InfoDict;

final class Info
{
    public function __construct(
        private readonly InfoDict $info,
    ) {}
}
