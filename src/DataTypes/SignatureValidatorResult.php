<?php

/**
 * @copyright 2017 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Torrent\DataTypes;

enum SignatureValidatorResult
{
    case Valid;
    case Invalid;
    case NotPresent;
}
