<?php

namespace Differ\Differ\JsonFormatter;

function render(array $records)
{
    $result = json_encode($records);
    return $result;
}
