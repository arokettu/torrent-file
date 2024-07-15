<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile\V2;

use Arokettu\Torrent\TorrentFile\Common\Attributes;

final class File
{
    public readonly string|null $piecesRoot;

    public function __construct(
        public readonly string $name,
        public readonly array $path,
        public readonly int $length,
        public readonly Attributes $attributes,
        public readonly string|null $piecesRootBin,
        public readonly array|null $symlinkPath,
    ) {
        $this->piecesRoot = $piecesRootBin ? bin2hex($piecesRootBin) : null;
    }
}
