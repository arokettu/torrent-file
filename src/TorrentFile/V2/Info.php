<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile\V2;

use Arokettu\Torrent\DataTypes\Internal\InfoDict;

final class Info
{
    private string|null $infoHash = null;
    private FileTree|null $fileTree = null;

    public function __construct(
        private readonly InfoDict $info,
    ) {
    }

    public function getInfoHash(bool $binary = false): string
    {
        $this->infoHash ??= hash('sha256', $this->info->infoString, true);
        return $binary ? $this->infoHash : bin2hex($this->infoHash);
    }

    public function getFileTree(): FileTree
    {
        $this->fileTree ??= new FileTree($this->info->info['file tree'], []);
        return $this->fileTree;
    }

    public function isDirectory(): bool
    {
        $fileTree = $this->info->info['file tree'];

        if (\count($fileTree) !== 1) {
            return true;
        }

        // check first file
        foreach ($fileTree as $file) {
            return isset($file['']['length']) === false;
        }

        // @codeCoverageIgnoreStart
        // should never happen
        throw new \LogicException('Unable to determine');
        // @codeCoverageIgnoreEnd
    }
}
