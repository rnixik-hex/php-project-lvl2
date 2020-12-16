<?php

$directoriesToScan = [
    '.',
    'Formatters',
    'Parsers',
];

array_walk(
    $directoriesToScan,
    fn($directory) => array_map(
        function ($fileName) {
            if ($fileName === __FILE__) {
                return;
            }
            require_once $fileName;
        },
        (array) glob(__DIR__ . '/' . $directory . '/*.php')
    ),
);
