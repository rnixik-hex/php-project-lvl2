<?php

namespace Differ\Differ;

use Tightenco\Collect\Support\Collection;

use function Differ\Formatters\Stylish\format as formatStylish;
use function Differ\Parsers\Json\parse as parseJson;
use function Differ\Parsers\Yaml\parse as parseYaml;

const PROP_KEY = 'key';
const PROP_OLD_VALUE = 'old_value';
const PROP_NEW_VALUE = 'new_value';
const PROP_DIFF_TYPE = 'diff_type';
const PROP_CHILDREN = 'children';

const DIFF_TYPE_ADDED = 'added';
const DIFF_TYPE_REMOVED = 'removed';
const DIFF_TYPE_UPDATED = 'update';
const DIFF_TYPE_UNCHANGED = 'unchanged';
const DIFF_TYPE_UPDATED_CHILDREN = 'updated_children';

const KEY_ROOT = 'root';

function genDiff(string $file1, string $file2, string $format = 'stylish'): string
{
    if (!is_file($file1) || !is_readable($file1)) {
        throw new \Exception("First file '$file1' is not readable");
    }
    if (!is_file($file2) || !is_readable($file2)) {
        throw new \Exception("Second file '$file2' is not readable");
    }

    $ext1 = pathinfo($file1)['extension'] ?? null;
    $ext2 = pathinfo($file2)['extension'] ?? null;

    if (!$ext1) {
        throw new \Exception("Cannot get extension from the first file '$file1'");
    }
    if (!$ext2) {
        throw new \Exception("Cannot get extension from the second file '$file2'");
    }

    $extensionToParsersMap = [
        'json' => fn($file) => parseJson($file),
        'yaml' => fn($file) => parseYaml($file),
        'yml' => fn($file) => parseYaml($file),
    ];

    if (empty($extensionToParsersMap[$ext1])) {
        throw new \Exception("Extension '$ext1' is unsupported'");
    }

    if (empty($extensionToParsersMap[$ext2])) {
        throw new \Exception("Extension '$ext2' is unsupported'");
    }

    $formatToFormattersMap = [
        'stylish' => fn($diffTree) => formatStylish($diffTree),
    ];

    if (empty($formatToFormattersMap[$format])) {
        throw new \Exception("Format '$format' is unsupported'");
    }

    $data1 = $extensionToParsersMap[$ext1]($file1);
    $data2 = $extensionToParsersMap[$ext2]($file2);

    $diffTree = getDiffTree($data1, $data2);

    return $formatToFormattersMap[$format]($diffTree);
}

function convertDiffToOutput($addedValues, $removedValues, $updatedValues, $firstValues): string
{
    $allKeys = array_keys(array_merge($addedValues, $removedValues, $updatedValues, $firstValues));

    // Use collection to sort keys without data mutation
    $keysCollection = new Collection($allKeys);
    $sortedKeys = $keysCollection->sort()->values()->toArray();

    $encodeKeyValueLine = function ($key, $value) {
        if (is_bool($value)) {
            return "$key: " . ($value ? 'true' : 'false') . "\n";
        }
        return "$key: $value\n";
    };

    $lines = array_map(function ($key) use (
        $addedValues,
        $removedValues,
        $updatedValues,
        $firstValues,
        $encodeKeyValueLine
    ) {
        if (array_key_exists($key, $addedValues)) {
            return "+ " . $encodeKeyValueLine($key, $addedValues[$key]);
        }
        if (array_key_exists($key, $removedValues)) {
            return "- " . $encodeKeyValueLine($key, $removedValues[$key]);
        }
        if (array_key_exists($key, $updatedValues)) {
            return "- " . $encodeKeyValueLine($key, $firstValues[$key])
                . "  + " . $encodeKeyValueLine($key, $updatedValues[$key]);
        }
        return "  " . $encodeKeyValueLine($key, $firstValues[$key]);
    }, $sortedKeys);

    return "{\n  " . implode('  ', $lines) . "}";
}

function getDiffTree($value1, $value2): array
{
    if (!is_array($value1) || !is_array($value2)) {
        if ($value1 === $value2) {
            return [
                PROP_KEY => KEY_ROOT,
                PROP_DIFF_TYPE => DIFF_TYPE_UNCHANGED,
                PROP_OLD_VALUE => $value1,
                PROP_NEW_VALUE => $value2,
            ];
        }

        return [
            PROP_KEY => KEY_ROOT,
            PROP_DIFF_TYPE => DIFF_TYPE_UPDATED,
            PROP_OLD_VALUE => $value1,
            PROP_NEW_VALUE => $value2,
        ];
    }

    $mergedKeys = array_merge(array_keys($value1), array_keys($value2));
    $keys = array_values(array_unique($mergedKeys));

    return array_map(function ($key) use ($value1, $value2) {
        if (array_key_exists($key, $value1) && array_key_exists($key, $value2)) {
            if ($value1[$key] === $value2[$key]) {
                return [
                    PROP_KEY => $key,
                    PROP_DIFF_TYPE => DIFF_TYPE_UNCHANGED,
                    PROP_OLD_VALUE => $value1[$key],
                    PROP_NEW_VALUE => $value2[$key],
                ];
            }
            if (is_array($value1[$key]) && is_array($value2[$key])) {
                return [
                    PROP_KEY => $key,
                    PROP_DIFF_TYPE => DIFF_TYPE_UPDATED_CHILDREN,
                    PROP_CHILDREN => getDiffTree($value1[$key], $value2[$key]),
                ];
            }
            return [
                PROP_KEY => $key,
                PROP_DIFF_TYPE => DIFF_TYPE_UPDATED,
                PROP_OLD_VALUE => $value1[$key],
                PROP_NEW_VALUE => $value2[$key],
            ];
        }
        if (array_key_exists($key, $value2)) {
            return [
                PROP_KEY => $key,
                PROP_DIFF_TYPE => DIFF_TYPE_ADDED,
                PROP_NEW_VALUE => $value2[$key],
            ];
        }
        if (array_key_exists($key, $value1)) {
            return [
                PROP_KEY => $key,
                PROP_DIFF_TYPE => DIFF_TYPE_REMOVED,
                PROP_OLD_VALUE => $value1[$key],
            ];
        }

        throw new \Exception("Unexpected branch of code");
    }, $keys);
}
