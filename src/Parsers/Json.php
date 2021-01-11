<?php

namespace Differ\Parsers\Json;

function parse(string $content): object
{
    return (object) json_decode($content);
}
