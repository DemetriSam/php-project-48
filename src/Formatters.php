<?php

namespace Differ\Differ;

use Funct\Collection;
use Differ\Differ\Records;
use Differ\Differ\StylishFormatter;
use Differ\Differ\PlainFormatter;
use Differ\Differ\JsonFormatter;

function render($records, $formatName)
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
