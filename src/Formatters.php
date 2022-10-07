<?php

namespace Gen\Diff;

use Funct\Collection;
use Gen\Diff\Records;
use Gen\Diff\StylishFormatter;
use Gen\Diff\PlainFormatter;
use Gen\Diff\JsonFormatter;

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
