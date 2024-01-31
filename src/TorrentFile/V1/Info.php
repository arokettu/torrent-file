<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile\V1;

use Arokettu\Torrent\DataTypes\Internal\DictObject;
use Arokettu\Torrent\DataTypes\Internal\InfoDict;
use Arokettu\Torrent\DataTypes\Internal\ListObject;
use Arokettu\Torrent\Exception\RuntimeException;
use Arokettu\Torrent\Helpers\ParseHelper;

final class Info
{
    private ?string $infoHash = null;
    private ?Files $files = null;

    public function __construct(
        private readonly InfoDict $info,
    ) {}

    public function getInfoHash(bool $binary = false): string
    {
        $this->infoHash ??= sha1($this->info->infoString, true);
        return $binary ? $this->infoHash : bin2hex($this->infoHash);
    }

    public function getFiles(): Files
    {
        return $this->files ??= $this->buildFiles();
    }

    private function buildFiles(): Files
    {
        $info = $this->info->info;
        $files = $info['files'];

        if ($files === null) {
            // assume single file

            $name = $info['name'];
            if ($name === null) {
                throw new RuntimeException('Invalid single-file torrent file: name is not set');
            }
            $length = $info['length'];
            if ($length === null) {
                throw new RuntimeException('Invalid single-file torrent file: length is not set');
            }
            $file = [
                'path' => new ListObject([$name]),
                'length' => $length,
            ];
            $attr = $info['attr'];
            if ($attr !== null) {
                $file['attr'] = $attr;
            }
            $sha1 = $info['sha1'];
            if ($sha1 !== null) {
                $file['sha1'] = ParseHelper::readSha1($sha1);
            }

            $files = new ListObject([new DictObject($file)]);
        }

        return new Files($files);
    }

    public function isDirectory(): bool
    {
        $info = $this->info->info;

        if ($info['length'] !== null) {
            return false;
        }

        $files = $info['files'];
        if ($files !== null) {
            return \count($files) > 1 || \count($files[0]['path']) > 1;
        }

        // @codeCoverageIgnoreStart
        // should never happen
        throw new \LogicException('Unable to determine');
        // @codeCoverageIgnoreEnd
    }
}
