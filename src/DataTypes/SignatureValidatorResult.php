<?php

declare(strict_types=1);

namespace Arokettu\Torrent\DataTypes;

enum SignatureValidatorResult
{
    case Valid;
    case Invalid;
    case NotPresent;
}
