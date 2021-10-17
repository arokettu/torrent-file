<?php

declare(strict_types=1);

namespace SandFox\Torrent\TorrentFile\Fields;

use SandFox\Torrent\Exception\InvalidArgumentException;

/**
 * @internal
 */
trait AnnounceList
{
    /**
     * @param string[]|string[][] $announceList
     * @return $this
     */
    public function setAnnounceList(array $announceList): self
    {
        foreach ($announceList as &$group) {
            if (\is_string($group)) {
                $group = [$group];
                continue;
            }

            if (!\is_array($group)) {
                throw new InvalidArgumentException(
                    'announce-list should be an array of strings or an array of arrays of strings'
                );
            }

            $group = array_values(array_unique($group));

            foreach ($group as $announce) {
                if (!\is_string($announce)) {
                    throw new InvalidArgumentException(
                        'announce-list should be an array of strings or an array of arrays of strings'
                    );
                }
            }
        }

        /** @var string[][] $announceList - string[] is converted to string[][] by now */

        $this->data['announce-list'] = array_values(
            array_unique(
                array_filter($announceList, fn ($v) => $v !== []),
                SORT_REGULAR
            )
        );

        return $this;
    }

    /**
     * @return string[][]
     */
    public function getAnnounceList(): array
    {
        return $this->data['announce-list'] ?? [];
    }
}
