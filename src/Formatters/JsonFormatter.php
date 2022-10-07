<?php

namespace Gen\Diff\JsonFormatter;

use Gen\Diff\Records;

function render($records)
{
    $result = json_encode($records, JSON_PRETTY_PRINT);
    return $result;
}
