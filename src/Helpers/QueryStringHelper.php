<?php

declare(strict_types=1);

namespace SandFox\Torrent\Helpers;

/**
 * @internal
 */
final class QueryStringHelper
{
    /**
     * @param string[][] $pairs
     * @return string
     */
    public static function build(array $pairs): string
    {
        return implode('&', array_map(function (array $pair) {
            return implode('=', $pair);
        }, $pairs));
    }
}
