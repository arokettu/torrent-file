<?php

declare(strict_types=1);

namespace SandFox\Torrent\Exception;

class OutOfBoundsException extends \OutOfBoundsException implements TorrentFileException
{
}
