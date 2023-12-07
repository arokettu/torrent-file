<?php

declare(strict_types=1);

namespace SandFox\Torrent\Tests;

function generate_files(): void
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

        for ($i = 0; $i < 1153; $i++) {
            for ($j = 0; $j < 983; $j++) {
                $index = ($index + $randomizer) % \count($words);
                fwrite($file, $words[$index]);
                fwrite($file, ' ');
            }
            fwrite($file, "\n\n");
        }

        fclose($file);
    }
}

function get_words(): array
{
    $wordsFile = TEST_ROOT . '/data/words.txt';

    $words = file_get_contents($wordsFile);

    return array_filter(explode("\n", $words));
}
