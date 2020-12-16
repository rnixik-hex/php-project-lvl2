<?php

namespace Differ\Formatters\Json;

function format(array $diffTree): string
{
    if (!$diffTree) {
        return '';
    }

    return json_encode($diffTree, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
