<?php

namespace Differ\Parsers\Yaml;

use Symfony\Component\Yaml\Yaml;

function parse(string $file): array
{
    $contents = file_get_contents($file);
    $map = Yaml::parse($contents, Yaml::PARSE_OBJECT_FOR_MAP);

    return (array) json_decode(json_encode($map), true);
}
