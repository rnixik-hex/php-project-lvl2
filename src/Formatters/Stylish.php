<?php

namespace Differ\Formatters\Stylish;

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

const INDENT_DOUBLE = '    ';
const INDENT_HALF = '  ';
const INDENT_ADD = '+ ';
const INDENT_REMOVE = '- ';

function format(array $diffTree): string
{
    if (count($diffTree) === 0) {
        return '';
    }

    return "{\n" . formatInner($diffTree, 1) . "}";
}

function formatInner(array $diffTree, int $depth): string
{
    $indent = str_repeat(INDENT_DOUBLE, $depth - 1) . INDENT_HALF;

    $diffTypeFormattersMap = [
        DIFF_TYPE_ADDED => fn($node) => $indent . INDENT_ADD . $node[PROP_KEY] . ': '
            . formatValue($node[PROP_NEW_VALUE], $depth),

        DIFF_TYPE_REMOVED => fn($node) => $indent . INDENT_REMOVE . $node[PROP_KEY] . ': '
            . formatValue($node[PROP_OLD_VALUE], $depth),

        DIFF_TYPE_UPDATED => fn($node) => $indent . INDENT_REMOVE . $node[PROP_KEY] . ': '
            . formatValue($node[PROP_OLD_VALUE], $depth)
            . $indent . INDENT_ADD . $node[PROP_KEY] . ': '
            . formatValue($node[PROP_NEW_VALUE], $depth),

        DIFF_TYPE_UNCHANGED => fn($node) => $indent . INDENT_HALF . $node[PROP_KEY] . ': '
            . formatValue($node[PROP_OLD_VALUE], $depth),

        DIFF_TYPE_UPDATED_CHILDREN => fn($node) =>  $indent . INDENT_HALF . $node[PROP_KEY] . ": {\n"
            . formatInner($node[PROP_CHILDREN], $depth + 1)
            . $indent . INDENT_HALF . "}\n",
    ];

    // Use collection to sort nodes without data mutation
    $nodesCollection = new Collection($diffTree);
    $sortedNodes = $nodesCollection->sortBy('key')->toArray();

    $lines = array_map(function ($node) use ($diffTypeFormattersMap): string {
        return $diffTypeFormattersMap[$node[PROP_DIFF_TYPE]]($node);
    }, $sortedNodes);

    return implode('', $lines);
}

/**
 * @param mixed $value
 * @param int $depth
 * @return string
 */
function formatValue($value, int $depth): string
{
    if (is_null($value)) {
        return "null\n";
    }

    if (is_bool($value)) {
        return ($value ? 'true' : 'false') . "\n";
    }

    if (is_array($value)) {
        return '[' . implode(', ', $value) . ']' . "\n";
    }

    if (is_object($value)) {
        $indent = str_repeat(INDENT_DOUBLE, $depth);
        $keys = array_keys(get_object_vars($value));

        return "{\n" . array_reduce($keys, function ($output, $key) use ($value, $indent, $depth): string {
                return $output . $indent . INDENT_DOUBLE . "$key: " . formatValue($value->{$key}, $depth + 1);
        }, '') . $indent . "}" . "\n";
    }

    return ((string) $value) . "\n";
}
