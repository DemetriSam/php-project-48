<?php

namespace Gen\Diff;

use Funct\Collection;

function prepareArrayForStringify($diff)
{
    return array_map(
        function($key, $value) {
            
        },
        array_keys($diff),
        $diff
    );
}