<?php

declare(strict_types=1);

namespace Arokettu\Torrent\FileSystem\HybridV1V2;

use Arokettu\Torrent\FileSystem\FileData as BaseFileData;
use Arokettu\Torrent\FileSystem\V1;
use Arokettu\Torrent\FileSystem\V2;

/**
 * @internal
 */
final class FileData extends BaseFileData
{
    public function process(): array
    {
        $params = [
            $this->path,
            $this->eventDispatcher,
            $this->pieceLength,
            /* pieceAlign */ 0, // should be 0 for V1 in hybrid mode, ignored on V2
            $this->detectExec,
            $this->detectSymlinks,
        ];

        $v1 = new V1\MultipleFileData(...$params);
        $v2 = new V2\FileData(...$params);

        $v1data = $v1->process();
        $v2data = $v2->process();

        // $v1data only has info dictionary, merge it
        $v2data['info'] = [...$v1data['info'], ...$v2data['info']];

        return $v2data;
    }
}
