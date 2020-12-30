<?php

namespace Differ\Formatters\Plain;

use Tightenco\Collect\Support\Collection;

use const Differ\Differ\DIFF_TYPE_ADDED;
use const Differ\Differ\DIFF_TYPE_REMOVED;
use const Differ\Differ\DIFF_TYPE_UNCHANGED;
use const Differ\Differ\DIFF_TYPE_UPDATED;
use const Differ\Differ\DIFF_TYPE_UPDATED_CHILDREN;
use const Differ\Differ\PROP_CHILDREN;
use const Differ\Differ\PROP_DIFF_TYPE;
use const Differ\Differ\PROP_KEY;
use const Differ\Differ\PROP_NEW_VALUE;
use const Differ\Differ\PROP_OLD_VALUE;

function format(array $diffTree): string
{
    if (count($diffTree) === 0) {
        return '';
    }

    return rtrim(formatInner($diffTree, ''), "\n");
}

function formatInner(array $diffTree, string $path): string
{
    $diffTypeFormattersMap = [
        DIFF_TYPE_ADDED => fn($node) => formatKeyWithPath($node[PROP_KEY], $path)
            . ' was added with value: ' . formatValue($node[PROP_NEW_VALUE]) . "\n",

        DIFF_TYPE_REMOVED => fn($node) =>formatKeyWithPath($node[PROP_KEY], $path) . " was removed\n",

        DIFF_TYPE_UPDATED => fn($node) => formatKeyWithPath($node[PROP_KEY], $path)
            . ' was updated. From ' . formatValue($node[PROP_OLD_VALUE])
            . ' to ' . formatValue($node[PROP_NEW_VALUE]) . "\n",

        DIFF_TYPE_UPDATED_CHILDREN =>
            fn($node) => formatInner($node[PROP_CHILDREN], appendKeyToPath($node[PROP_KEY], $path)),

        DIFF_TYPE_UNCHANGED => fn($node) => '',
    ];

    // Use collection to sort nodes without data mutation
    $nodesCollection = new Collection($diffTree);
    $sortedNodes = $nodesCollection->sortBy('key')->toArray();

    return array_reduce($sortedNodes, function ($output, $node) use ($diffTypeFormattersMap) {
        $formatter = $diffTypeFormattersMap[$node[PROP_DIFF_TYPE]];
        return $output . $formatter($node);
    }, '');
}

function appendKeyToPath(string $key, string $path): string
{
    if ($path !== '') {
        return $path . '.' . $key;
    }

    return $key;
}

function formatKeyWithPath(string $key, string $path): string
{
    $fullPath = appendKeyToPath($key, $path);

    return "Property '$fullPath'";
}

/**
 * @param mixed $value
 * @return string
 */
function formatValue($value): string
{
    if (is_object($value)) {
        return formatValue((array) $value);
    }

    $variableType = gettype($value);

    $typeFormattersMap = [
        'boolean' => fn($value) => $value ? 'true' : 'false',
        'string' => fn($value) => "'$value'",
        'NULL' => fn($value) => 'null',
    ];

    if (isset($typeFormattersMap[$variableType])) {
        return $typeFormattersMap[$variableType]($value);
    }

    if (!is_array($value)) {
        return ((string) $value);
    }

    return "[complex value]";
}
