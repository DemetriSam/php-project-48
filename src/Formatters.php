<?php

namespace Differ\Formatters;

use Differ\Formatters\StylishFormatter;
use Differ\Formatters\PlainFormatter;
use Differ\Formatters\JsonFormatter;

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
