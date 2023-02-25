<?php

declare(strict_types=1);

namespace Arokettu\Torrent\V2;

use Arokettu\Torrent\Common\Attributes;

final class File
{
    public readonly ?string $piecesRoot;

    public function __construct(
        public readonly string $name,
        public readonly array $path,
        public readonly int $length,
        public readonly Attributes $attributes,
        public readonly ?string $piecesRootBin,
        public readonly ?array $symlinkPath,
    ) {
        $this->piecesRoot = $piecesRootBin ? bin2hex($piecesRootBin) : null;
    }
}
