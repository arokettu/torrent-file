<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Exception;

class BadMethodCallException extends \BadMethodCallException implements TorrentFileException
{
}
