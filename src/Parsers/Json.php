<?php

namespace Differ\Parsers\Json;

function parse(string $contents): object
{
    return (object) json_decode($contents);
}
