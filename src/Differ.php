<?php

namespace Differ\Differ;

use function Differ\Formatters\formatDiffTree;
use function Differ\Parsers\parseContents;

use const Differ\Parsers\CONTENTS_FORMAT_JSON;
use const Differ\Parsers\CONTENTS_FORMAT_YAML;

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

function genDiff(string $filepath1, string $filepath2, string $format = 'stylish'): string
{
    if (!is_file($filepath1) || !is_readable($filepath1)) {
        throw new \Exception("First file '$filepath1' is not readable");
    }
    if (!is_file($filepath2) || !is_readable($filepath2)) {
        throw new \Exception("Second file '$filepath2' is not readable");
    }

    $ext1 = pathinfo($filepath1)['extension'] ?? null;
    $ext2 = pathinfo($filepath2)['extension'] ?? null;

    if (!$ext1) {
        throw new \Exception("Cannot get extension from the first file '$filepath1'");
    }
    if (!$ext2) {
        throw new \Exception("Cannot get extension from the second file '$filepath2'");
    }

    $extensionToFormatMap = [
        'json' => CONTENTS_FORMAT_JSON,
        'yaml' => CONTENTS_FORMAT_YAML,
        'yml' => CONTENTS_FORMAT_YAML,
    ];

    if (empty($extensionToFormatMap[$ext1])) {
        throw new \Exception("Extension '$ext1' is unsupported'");
    }

    if (empty($extensionToFormatMap[$ext2])) {
        throw new \Exception("Extension '$ext2' is unsupported'");
    }

    $contents1 = file_get_contents($filepath1);
    if ($contents1 === false) {
        throw new \Exception("Cannot read the first file '$filepath1'");
    }

    $contents2 = file_get_contents($filepath2);
    if ($contents2 === false) {
        throw new \Exception("Cannot read the second file '$filepath2'");
    }

    $data1 = parseContents($contents1, $extensionToFormatMap[$ext1]);
    $data2 = parseContents($contents2, $extensionToFormatMap[$ext2]);

    $diffTree = getDiffTree($data1, $data2);

    return formatDiffTree($diffTree, $format);
}

/**
 * @param mixed $value1
 * @param mixed $value2
 * @return array
 * @throws \Exception
 */
function getDiffTree($value1, $value2): array
{
    if (!is_array($value1) || !is_array($value2)) {
        if ($value1 === $value2) {
            return [
                PROP_KEY => KEY_ROOT,
                PROP_DIFF_TYPE => DIFF_TYPE_UNCHANGED,
                PROP_OLD_VALUE => $value1,
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
