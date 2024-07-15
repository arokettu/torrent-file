<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile\V1;

use Arokettu\Torrent\TorrentFile\Common\Attributes;

final class File
{
    public readonly string $name;
    public readonly string|null $sha1;

    public function __construct(
        public readonly array $path,
        public readonly int $length,
        public readonly Attributes $attributes,
        public readonly string|null $sha1bin,
        public readonly array|null $symlinkPath,
    ) {
        $this->name = $this->path[array_key_last($this->path)];
        $this->sha1 = $sha1bin ? bin2hex($sha1bin) : null;
    }
}
