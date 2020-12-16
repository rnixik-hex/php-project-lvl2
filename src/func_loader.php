<?php

$directoriesToScan = [
    '.',
    'Formatters',
    'Parsers',
];

/** @phpstan-ignore-next-line */
array_map(
    fn($directory) => array_map(
        function ($fileName) {
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
