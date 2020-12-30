<?php

/*
 * Auto loads all files (with functions) from specified directories.
 */

$directoriesToScan = [
    '.',
    'Formatters',
    'Parsers',
];

/** @phpstan-ignore-next-line */
array_map(
    fn($directory) => array_map(
        function ($fileName): void {
            if ($fileName === __FILE__) {
                return;
            }
            /** @phpstan-ignore-next-line */
            require_once $fileName;
        },
        (array) glob(__DIR__ . '/' . $directory . '/*.php')
    ),
    $directoriesToScan
);
