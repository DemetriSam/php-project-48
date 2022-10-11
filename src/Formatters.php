<?php

namespace Differ\Differ;

use Differ\Differ\StylishFormatter;
use Differ\Differ\PlainFormatter;
use Differ\Differ\JsonFormatter;

function render(array $records, string $formatName)
{
    switch ($formatName) {
        case 'stylish':
            return StylishFormatter\render($records);

        case 'plain':
            return PlainFormatter\render($records);

        case 'json':
            return JsonFormatter\render($records);

        default:
            throw new \Exception("The '$formatName' format is unknown");
    }
}

function toString(mixed $input, bool $trim = true)
{
    $exported = var_export($input, true) === 'NULL' ? 'null' : var_export($input, true);

    return $trim ? trim($exported, "'") : $exported;
}
