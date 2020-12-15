<?php

$directoriesToScan = [
    '.',
    'Formatters',
    'Parsers',
];

foreach ($directoriesToScan as $directory) {
    foreach ((array) glob(__DIR__ . '/' . $directory . '/*.php') as $fileName) {
        if ($fileName === __FILE__) {
            continue;
        }
        include_once $fileName;
    }
}
