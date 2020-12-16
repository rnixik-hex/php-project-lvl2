<?php

namespace Differ\Parsers\Yaml;

use Symfony\Component\Yaml\Yaml;

function parse(string $contents): array
{
    $map = Yaml::parse($contents, Yaml::PARSE_OBJECT_FOR_MAP);

    // Convert objects (including nested) to arrays
    return (array) json_decode((string) json_encode($map), true);
}
