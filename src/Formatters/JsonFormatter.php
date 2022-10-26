<?php

namespace Differ\Formatters\JsonFormatter;

function render(array $records)
{
    $result = json_encode($records);
    return $result;
}
