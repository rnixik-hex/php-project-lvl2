<?php

namespace Differ\Differ;

use Tightenco\Collect\Support\Collection;

function genDiff(string $file1, string $file2): string
{
    if (!is_file($file1) || !is_readable($file1)) {
        throw new \Exception("First file '$file1' is not readable");
    }
    if (!is_file($file2) || !is_readable($file2)) {
        throw new \Exception("Second file '$file2' is not readable");
    }

    $contents1 = file_get_contents($file1);
    $contents2 = file_get_contents($file2);

    $data1 = (array) json_decode($contents1, true);
    $data2 = (array) json_decode($contents2, true);

    [$addedValues, $removedValues, $updatedValues, $firstValues] = getDiff($data1, $data2);

    return convertDiffToOutput($addedValues, $removedValues, $updatedValues, $firstValues);
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
                . "+ " . $encodeKeyValueLine($key, $updatedValues[$key]);
        }
        return "  " . $encodeKeyValueLine($key, $firstValues[$key]);
    }, $sortedKeys);

    return implode('', $lines);
}

function getDiff(array $data1, array $data2): array
{
    $addedValues = array_filter(
        $data2,
        fn($value, $key) => !array_key_exists($key, $data1),
        ARRAY_FILTER_USE_BOTH
    );
    $removedValues = array_filter(
        $data1,
        fn($value, $key) => !array_key_exists($key, $data2),
        ARRAY_FILTER_USE_BOTH
    );
    $updatedValues = array_filter(
        $data2,
        fn($value, $key) => array_key_exists($key, $data1) && $data1[$key] !== $value,
        ARRAY_FILTER_USE_BOTH
    );

    return [
        $addedValues,
        $removedValues,
        $updatedValues,
        $data1
    ];
}
