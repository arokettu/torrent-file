<?php

declare(strict_types=1);

namespace Arokettu\Torrent\V1;

use Arokettu\Torrent\Common\Attributes;

final class File
{
    public readonly ?string $sha1;

    public function __construct(
        public readonly array $path,
        public readonly int $length,
        public readonly Attributes $attributes,
        public readonly ?string $sha1bin,
        public readonly ?array $symlinkPath,
    ) {
        $this->sha1 = $sha1bin ? bin2hex($sha1bin) : null;
    }
}
