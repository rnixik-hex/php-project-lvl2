<?php

namespace Differ\Parsers\Json;

function parse(string $file): array
{
    $contents = file_get_contents($file);

    return (array) json_decode($contents, true);
}
