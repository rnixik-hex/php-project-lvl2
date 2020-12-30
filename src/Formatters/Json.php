<?php

namespace Differ\Formatters\Json;

function format(array $diffTree): string
{
    if (count($diffTree) === 0) {
        return '';
    }

    return (string) json_encode($diffTree, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}
