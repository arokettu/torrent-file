<?php

/**
 * @copyright 2017 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Torrent\FileSystem;

final class FileDataProgressEvent
{
    /**
     * @param int $total Total size
     * @param int $done Processed size
     * @param string $fileName Current file name
     */
    public function __construct(
        public readonly int $total,
        public readonly int $done,
        public readonly string $fileName,
    ) {
    }
}
