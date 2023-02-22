<?php

declare(strict_types=1);

namespace Arokettu\Torrent\V1;

use Arokettu\Torrent\Common\Attributes;
use Arokettu\Torrent\DataTypes\Internal\ListObject;

final class File
{
    public readonly array $path;
    public readonly ?array $symlinkPath;
    public readonly ?string $sha1;

    public function __construct(
        ListObject $path,
        public readonly int $length,
        public readonly Attributes $attributes,
        public readonly ?string $sha1bin,
        ?ListObject $symlinkPath,
    ) {
        $this->path = $path->toArray();
        $this->symlinkPath = $symlinkPath?->toArray();
        $this->sha1 = $sha1bin ? bin2hex($sha1bin) : null;
    }
}
