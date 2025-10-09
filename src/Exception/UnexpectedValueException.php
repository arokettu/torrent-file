<?php

/**
 * @copyright 2017 Anton Smirnov
 * @license MIT https://spdx.org/licenses/MIT.html
 */

declare(strict_types=1);

namespace Arokettu\Torrent\Exception;

final class UnexpectedValueException extends \UnexpectedValueException implements TorrentFileException
{
}
