<?php

$directoriesToScan = [
    '.',
    'Formatters',
    'Parsers',
];

array_map(
    fn($directory) => array_map(
        function ($fileName) {
            if ($fileName === __FILE__) {
                return;
            }
            include_once $fileName;
        },
        (array) glob(__DIR__ . '/' . $directory . '/*.php')
    ),
    $directoriesToScan
);
