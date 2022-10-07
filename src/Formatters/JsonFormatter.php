<?php

namespace Differ\Differ\JsonFormatter;

use Differ\Differ\Records;

function render($records)
{
    $result = json_encode($records, JSON_PRETTY_PRINT);
    return $result;
}
