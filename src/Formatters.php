<?php

namespace Differ\Formatters;

use function Differ\Formatters\Json\format as formatJson;
use function Differ\Formatters\Plain\format as formatPlain;
use function Differ\Formatters\Stylish\format as formatStylish;

function formatDiffTree(array $diffTree, string $format): string
{
    $formatToFormattersMap = [
        'stylish' => fn($diffTree) => formatStylish($diffTree),
        'plain' => fn($diffTree) => formatPlain($diffTree),
        'json' => fn($diffTree) => formatJson($diffTree),
    ];

    if (empty($formatToFormattersMap[$format])) {
        throw new \Exception("Format '$format' is unsupported'");
    }

    return $formatToFormattersMap[$format]($diffTree);
}
