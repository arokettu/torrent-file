<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile\Fields;

use Arokettu\Torrent\DataTypes\UriList;

trait UrlList
{
    private ?UriList $urlList = null;

    abstract private function getField(string $key, mixed $default = null): mixed;
    abstract private function setField(string $key, mixed $value): void;

    public function getUrlList(): UriList
    {
        $urlList = $this->getField('url-list', []);
        // additional handling in case url-list is a string not array of strings
        return $this->urlList ??= new UriList(\is_array($urlList) ? $urlList : [$urlList]);
    }

    /**
     * @param UriList|iterable<string>|null $value
     */
    public function setUrlList(UriList|iterable|null $value): self
    {
        // always store as list
        $this->setField(
            'url-list',
            $this->urlList = $value instanceof UriList ? $value : UriList::fromIterable($value ?? [])
        );
        return $this;
    }
}
