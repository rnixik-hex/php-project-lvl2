<?php

namespace Differ\Parsers;

use function Differ\Parsers\Json\parse as parseJson;
use function Differ\Parsers\Yaml\parse as parseYaml;

const CONTENTS_FORMAT_JSON = 'json';
const CONTENTS_FORMAT_YAML = 'yaml';

function parseContent(string $contents, string $contentsFormat): object
{
    $formatToParsersMap = [
        CONTENTS_FORMAT_JSON => fn($contents) => parseJson($contents),
        CONTENTS_FORMAT_YAML => fn($contents) => parseYaml($contents),
    ];

    if (!isset($formatToParsersMap[$contentsFormat])) {
        throw new \Exception("Format '$contentsFormat' is unsupported'");
    }

    return $formatToParsersMap[$contentsFormat]($contents);
}
