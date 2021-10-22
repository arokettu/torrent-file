<?php

declare(strict_types=1);

namespace SandFox\Torrent\Exception;

class BadMethodCallException extends \BadMethodCallException implements TorrentFileException
{
}
