<?php

declare(strict_types=1);

namespace Arokettu\Torrent\TorrentFile\Fields;

use Arokettu\Torrent\DataTypes\UriList;

/**
 * @internal
 */
trait UrlList
{
    private UriList|null $urlList = null;

    abstract private function getField(string $key): mixed;
    abstract private function setField(string $key, mixed $value): void;

    public function getUrlList(): UriList
    {
        // additional handling in case url-list is a string not array of strings
        return $this->urlList ??= UriList::fromInternalUrlList($this->getField('url-list'));
    }

    /**
     * @param UriList|iterable<string>|null $value
     */
    public function setUrlList(UriList|iterable|null $value): self
    {
        // always store as list
        $this->urlList = UriList::fromIterable($value ?? []);
        $this->setField('url-list', $this->urlList);
        return $this;
    }
}
