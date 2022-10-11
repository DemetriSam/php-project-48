<?php

namespace Differ\Differ\JsonFormatter;

function render($records)
{
    $result = json_encode($records);
    return $result;
}
