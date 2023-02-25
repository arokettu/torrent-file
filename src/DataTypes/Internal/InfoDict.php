<?php

declare(strict_types=1);

namespace Arokettu\Torrent\DataTypes\Internal;

use Arokettu\Bencode\Encoder;

/**
 * @internal
 */
final class InfoDict
{
    public readonly string $infoString;

    public function __construct(
        public readonly DictObject $info
    ) {
        unset($this->infoString); // lazy creation
    }

    public function __get(string $name): mixed
    {
        return match ($name) {
            'infoString' => $this->infoString = (new Encoder())->encode($this->info),
        };
    }
}
