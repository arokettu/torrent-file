<?php

declare(strict_types=1);

namespace Arokettu\Torrent\Tests;

use Arokettu\Torrent\TorrentFile;

function generate_files1(): void
{
    $filesPath = TEST_ROOT . '/data/files';

    if (!is_dir($filesPath)) {
        mkdir($filesPath);
    }

    foreach (['file1.txt' => 5641, 'file2.txt' => 8447, 'file3.txt' => 1559] as $filename => $randomizer) {
        $path = "{$filesPath}/{$filename}";

        if (is_file($path)) {
            continue;
        }

        $words = $words ?? get_words();

        $file = fopen($path, 'w');

        $index = 0;

        for ($i = 0; $i < 1153; ++$i) {
            for ($j = 0; $j < 983; ++$j) {
                $index = ($index + $randomizer) % \count($words);
                fwrite($file, $words[$index]);
                fwrite($file, ' ');
            }
            fwrite($file, "\n\n");
        }

        fclose($file);
    }
}

function generate_files2(): void
{
    $filesPath = TEST_ROOT . '/data/files';
    $files2Path = TEST_ROOT . '/data/files2';

    $paths = [
        $files2Path,
        $files2Path . '/dir1',
        $files2Path . '/dir2',
        $files2Path . '/dir3',
        $files2Path . '/dir4',
        $files2Path . '/dir5',
        $files2Path . '/dir6',
    ];

    foreach ($paths as $path) {
        if (!is_dir($path)) {
            mkdir($path);
        }
    }

    // @phpcs:disable PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnSymlink
    // External symlinks: should always become files

    @symlink($filesPath . '/file1.txt', $files2Path . '/dir1/file1.txt');
    @symlink($filesPath . '/file2.txt', $files2Path . '/dir3/file2.txt');
    @symlink($filesPath . '/file3.txt', $files2Path . '/dir5/file3.txt');

    // relative link
    @symlink('../dir1/file1.txt', $files2Path . '/dir2/file1.txt');
    // absolute link
    @symlink($files2Path . '/dir3/file2.txt', $files2Path . '/dir2/file2.txt');

    // @phpcs:enable PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnSymlink

    // align file to a megabyte exactly
    $perfectlyAligned = file_get_contents($files2Path . '/dir1/file1.txt');
    $perfectlyAlignedLength = intdiv(\strlen($perfectlyAligned), 2 ** 20);
    $perfectlyAligned = substr($perfectlyAligned, 0, $perfectlyAlignedLength * 2 ** 20);
    file_put_contents($files2Path . '/dir4/aligned.txt', $perfectlyAligned);

    // executable file
    $exec = file_get_contents($files2Path . '/dir5/file3.txt');
    $exec = "#!/usr/bin/env cat\n\n" . $exec;
    file_put_contents($files2Path . '/dir6/exec.txt', $exec);

    @chmod($files2Path . '/dir6/exec.txt', 0755);
}

function generate_files3(): void
{
    file_put_contents(TEST_ROOT . '/data/empty_file.txt', '');
}

function generate_files4(): void
{
    $filesPath = TEST_ROOT . '/data/files';
    $files4Path = TEST_ROOT . '/data/4444';

    $paths = [
        $files4Path,
        $files4Path . '/dir',
        $files4Path . '/2',
    ];

    foreach ($paths as $path) {
        if (!is_dir($path)) {
            mkdir($path);
        }
    }

    // @phpcs:disable PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnSymlink
    // External symlinks: should always become files

    @symlink($filesPath . '/file1.txt', $files4Path . '/dir/file1.txt');
    @symlink($filesPath . '/file1.txt', $files4Path . '/dir/1111');
    @symlink($filesPath . '/file1.txt', $files4Path . '/dir/222');
    @symlink($filesPath . '/file1.txt', $files4Path . '/dir/33');
    @symlink($filesPath . '/file1.txt', $files4Path . '/dir/4');
    @symlink($filesPath . '/file1.txt', $files4Path . '/2/file2.txt');
    @symlink($filesPath . '/file1.txt', $files4Path . '/2/1');
    @symlink($filesPath . '/file1.txt', $files4Path . '/2/-22');
    @symlink($filesPath . '/file1.txt', $files4Path . '/2/0333');
    @symlink($filesPath . '/file1.txt', $files4Path . '/2/0x4444');

    // @phpcs:enable PHPCS_SecurityAudit.BadFunctions.FilesystemFunctions.WarnSymlink
}

function get_words(): array
{
    $wordsFile = TEST_ROOT . '/data/words.txt';

    $words = file_get_contents($wordsFile);

    return array_filter(explode("\n", $words));
}

function build_magnet_link(array $components): string
{
    return 'magnet:?' . implode('&', $components);
}

function export_test_data(mixed $data): string
{
    if (\is_array($data)) {
        $export = "[\n";
        if (array_is_list($data)) {
            foreach ($data as $value) {
                $export .= export_test_data($value);
                $export .= ",\n";
            }
        } else {
            foreach ($data as $key => $value) {
                $export .= export_test_data($key);
                $export .= ' => ';
                $export .= export_test_data($value);
                $export .= ",\n";
            }
        }
        $export .= ']';

        return $export;
    }

    if (\is_string($data) && $data !== '' && !ctype_print($data)) {
        return 'base64_decode("' . base64_encode($data) . '")';
    }

    return var_export($data, true);
}

function raw_torrent_data(TorrentFile $torrent): array
{
    return $torrent->getRawData()->toArray();
}

function recursive_iterator_to_array(\Traversable $iterator, bool $preserveKeys = true): array
{
    if ($iterator instanceof \IteratorAggregate) {
        return recursive_iterator_to_array($iterator->getIterator());
    }

    if (!($iterator instanceof \RecursiveIterator)) {
        return iterator_to_array($iterator, $preserveKeys);
    }

    return iterator_to_array((function (\RecursiveIterator $iterator) {
        $iterator->rewind();

        while ($iterator->valid()) {
            yield $iterator->key() =>
                $iterator->hasChildren() ? recursive_iterator_to_array($iterator->getChildren()) : $iterator->current();
            $iterator->next();
        }
    })($iterator));
}
