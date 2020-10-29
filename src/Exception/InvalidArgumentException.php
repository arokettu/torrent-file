<?php

declare(strict_types=1);

namespace SandFox\Torrent\Exception;

class InvalidArgumentException extends \InvalidArgumentException implements TorrentFileException
{
}
