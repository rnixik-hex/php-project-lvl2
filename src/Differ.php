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

    $contents1 = file_get_contents($filepath1);
    if ($contents1 === false) {
        throw new \Exception("Cannot read the first file '$filepath1'");
    }

    $contents2 = file_get_contents($filepath2);
    if ($contents2 === false) {
        throw new \Exception("Cannot read the second file '$filepath2'");
    }

    $ext1 = pathinfo($filepath1)['extension'] ?? null;
    $ext2 = pathinfo($filepath2)['extension'] ?? null;

    $data1 = parseContents($contents1, getFileFormatFromExtension($ext1));
    $data2 = parseContents($contents2, getFileFormatFromExtension($ext2));

    $diffTree = getDiffTree($data1, $data2);

    return formatDiffTree($diffTree, $format);
}

/**
 * @param mixed $data1
 * @param mixed $data2
 * @return array
 * @throws \Exception
 */
function getDiffTree($data1, $data2): array
{
    if (!is_array($data1) || !is_array($data2)) {
        if ($data1 === $data2) {
            return [
                PROP_KEY => KEY_ROOT,
                PROP_DIFF_TYPE => DIFF_TYPE_UNCHANGED,
                PROP_OLD_VALUE => $data1,
            ];
        }

        return [
            PROP_KEY => KEY_ROOT,
            PROP_DIFF_TYPE => DIFF_TYPE_UPDATED,
            PROP_OLD_VALUE => $data1,
            PROP_NEW_VALUE => $data2,
        ];
    }

    $mergedKeys = array_merge(array_keys($data1), array_keys($data2));
    $keys = array_values(array_unique($mergedKeys));

    return array_map(function ($key) use ($data1, $data2) {
        if (!array_key_exists($key, $data1)) {
            return [
                PROP_KEY => $key,
                PROP_DIFF_TYPE => DIFF_TYPE_ADDED,
                PROP_NEW_VALUE => $data2[$key],
            ];
        }
        if (!array_key_exists($key, $data2)) {
            return [
                PROP_KEY => $key,
                PROP_DIFF_TYPE => DIFF_TYPE_REMOVED,
                PROP_OLD_VALUE => $data1[$key],
            ];
        }
        if ($data1[$key] === $data2[$key]) {
            return [
                PROP_KEY => $key,
                PROP_DIFF_TYPE => DIFF_TYPE_UNCHANGED,
                PROP_OLD_VALUE => $data1[$key],
            ];
        }
        if (is_array($data1[$key]) && is_array($data2[$key])) {
            return [
                PROP_KEY => $key,
                PROP_DIFF_TYPE => DIFF_TYPE_UPDATED_CHILDREN,
                PROP_CHILDREN => getDiffTree($data1[$key], $data2[$key]),
            ];
        }
        return [
            PROP_KEY => $key,
            PROP_DIFF_TYPE => DIFF_TYPE_UPDATED,
            PROP_OLD_VALUE => $data1[$key],
            PROP_NEW_VALUE => $data2[$key],
        ];
    }, $keys);
}

function getFileFormatFromExtension(?string $extension): string
{
    $extensionToFormatMap = [
        'json' => CONTENTS_FORMAT_JSON,
        'yaml' => CONTENTS_FORMAT_YAML,
        'yml' => CONTENTS_FORMAT_YAML,
    ];

    if (empty($extensionToFormatMap[$extension])) {
        throw new \Exception("Extension '$extension' is unsupported'");
    }

    return $extensionToFormatMap[$extension];
}
