<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Exception;

class OutOfBoundsException extends \OutOfBoundsException implements TorrentFileException
{
}
