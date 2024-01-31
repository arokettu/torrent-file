<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Helpers;

use Arokettu\Torrent\Exception\RuntimeException;

/**
 * @internal
 */
final class ParseHelper
{
    public static function readSha1(string|null $sha1): string|null
    {
        if ($sha1 === null || \strlen($sha1) === 20) {
            return $sha1; // bytes or null
        }
        if (\strlen($sha1) === 40) {
            // hex encoded - some legacy archive torrents
            return @hex2bin($sha1) ?:
                throw new RuntimeException('Invalid sha1 hex digits');
        }
        throw new RuntimeException(
            'Invalid sha1 field: must be 20 bytes (standard) or 40 hex digits (legacy)'
        );
    }
}
