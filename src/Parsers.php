<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseJson(string $file): array
{
    $contents = file_get_contents($file);

    return (array) json_decode($contents, true);
}

function parseYaml(string $file): array
{
    $contents = file_get_contents($file);

    return (array) Yaml::parse($contents, Yaml::PARSE_OBJECT_FOR_MAP);
}
