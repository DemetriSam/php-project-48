<?php

namespace Differ\Differ\JsonFormatter;

function render($records)
{
    $result = json_encode($records, JSON_PRETTY_PRINT);
    return $result;
}
