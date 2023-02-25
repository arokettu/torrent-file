<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile;

use Arokettu\Torrent\DataTypes\Internal\DictObject;
use Arokettu\Torrent\DataTypes\Internal\ListObject;
use Arokettu\Torrent\Exception\RuntimeException;
use Arokettu\Torrent\MetaVersion;
use Arokettu\Torrent\V1;
use Arokettu\Torrent\V2;

trait FilesMethods
{
    private ?V1\Files $filesV1 = null;

    abstract private function getInfoField(string $key, mixed $default = null): mixed;

    /**
     * @psalm-return ($version is MetaVersion::V1 ? V1\Files : V2\Files)
     */
    public function getFiles(MetaVersion $version): V1\Files|V2\Files
    {
        return match ($version) {
            MetaVersion::V1 => $this->getFilesV1(),
            MetaVersion::V2 => $this->getFilesV2(),
        };
    }

    private function getFilesV1(): V1\Files
    {
        return $this->filesV1 ??= $this->buildFilesV1();
    }

    private function buildFilesV1(): V1\Files
    {
        $files = $this->getInfoField('files');

        if ($files === null) {
            // assume single file

            $name = $this->getInfoField('name');
            if ($name === null) {
                throw new RuntimeException('Invalid single-file torrent file: name is not set');
            }
            $length = $this->getInfoField('length');
            if ($length === null) {
                throw new RuntimeException('Invalid single-file torrent file: length is not set');
            }
            $file = [
                'path' => new ListObject([$name]),
                'length' => $length,
            ];
            $attr = $this->getInfoField('attr');
            if ($attr !== null) {
                $file['attr'] = $attr;
            }
            $sha1 = $this->getInfoField('sha1');
            if ($sha1 !== null) {
                $file['sha1'] = $sha1;
            }

            $files = new ListObject([new DictObject($file)]);
        }

        return new V1\Files($files);
    }

    private function getFilesV2(): V2\Files
    {
        $files = $this->getInfoField('file tree');

        return new V2\Files($files, []);
    }
}
