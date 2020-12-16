<?php

namespace Differ\Parsers\Json;

function parse(string $contents): array
{
    return (array) json_decode($contents, true);
}
