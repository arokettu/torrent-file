<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Helpers;

final class MathHelper
{
    private const SHA256_EMPTY = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0"; // 32 zeros

    public static function isPow2(int $value): bool
    {
        return $value > 0 && ($value & ($value - 1)) === 0;
    }

    /**
     * Integer binary logarithm
     */
    public static function log2i(int $value): int
    {
        if ($value <= 0) {
            // @codeCoverageIgnoreStart
            // should never happen
            throw new \LogicException('Value must be >= 1');
            // @codeCoverageIgnoreEnd
        }

        /** @noinspection PhpStatementHasEmptyBodyInspection */
        for ($l = 0; $value > 0; $value >>= 1, $l += 1);

        return $l - 1;
    }

    public static function merkleTreeLevelSha256(array $hashes, int $level): array
    {
        $count = \count($hashes);
        $twoPowLevel = 2 ** $level;

        if ($count < $twoPowLevel) {
            // @codeCoverageIgnoreStart
            // should never happen
            throw new \LogicException('Not enough hashes for level');
            // @codeCoverageIgnoreEnd
        }

        // do not add zeros on 0 level
        if ($level === 0) {
            return $hashes;
        }

        // normalize a number of hashes
        if ($count % $twoPowLevel !== 0) {
            $partials = intdiv($count, $twoPowLevel) + 1;
            $missing = $partials * $twoPowLevel - $count;
            for ($i = 0; $i < $missing; ++$i) {
                $hashes[] = self::SHA256_EMPTY;
            }
        }

        for ($i = 0; $i < $level; ++$i) {
            $hashes = self::merkleTreeSingleLevelSha256($hashes);
        }

        return $hashes;
    }

    public static function merkleTreeRootSha256(array $hashes): string
    {
        // normalize a number of hashes
        $count = \count($hashes);

        if ($count === 0) {
            // @codeCoverageIgnoreStart
            // should never happen
            throw new \LogicException('Hashes array must not be empty');
            // @codeCoverageIgnoreEnd
        }

        if (!self::isPow2($count)) {
            $pow = self::log2i($count) + 1;
            $missing = 2 ** $pow - $count;
            for ($i = 0; $i < $missing; ++$i) {
                $hashes[] = self::SHA256_EMPTY;
            }
        }

        while (\count($hashes) > 1) {
            $hashes = self::merkleTreeSingleLevelSha256($hashes);
        }

        return $hashes[0];
    }

    private static function merkleTreeSingleLevelSha256(array $hashes): array
    {
        $newHashes = [];
        for ($i = 0; $i < \count($hashes); $i += 2) {
            $newHashes[] = hash('sha256', $hashes[$i] . $hashes[$i + 1], true);
        }
        return $newHashes;
    }
}
