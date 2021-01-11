<?php

namespace Differ\Parsers\Yaml;

use Symfony\Component\Yaml\Yaml;

function parse(string $content): object
{
    return (object) Yaml::parse($content, Yaml::PARSE_OBJECT_FOR_MAP);
}
