<?php

namespace Differ\Parsers\Yaml;

use Symfony\Component\Yaml\Yaml;

function parse(string $contents): object
{
    return (object) Yaml::parse($contents, Yaml::PARSE_OBJECT_FOR_MAP);
}
