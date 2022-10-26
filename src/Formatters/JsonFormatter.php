<?php

namespace Differ\Formatters\JsonFormatter;

function render(array $records): string
{
    return json_encode($records);
}
