<?php

declare(strict_types=1);

namespace Arokettu\Torrent\V1;

use Arokettu\Torrent\Common\Attributes;
use Arokettu\Torrent\DataTypes\Internal\ListObject;

final class File
{
    public readonly array $path;
    public readonly ?array $symlinkPath;

    public function __construct(
        ListObject $path,
        public readonly int $length,
        public readonly Attributes $attributes,
        public readonly ?string $sha1,
        ?ListObject $symlinkPath,
    ) {
        $this->path = $path->toArray();
        $this->symlinkPath = $symlinkPath?->toArray();
    }
}
