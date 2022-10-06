<?php

namespace Gen\Diff\PlainFormatter;

use Gen\Diff\Records;

function render($records)
{

    $lines = reduce(
        function($init, $records) {
            $lines = array_map(function($record) {
                
                $path = Records\getCurrenPath($record);
                $status = Records\getStatus($record);
                $value = is_array(Records\getValue($record)) ?
                        '[complex value]' :
                        Records\toString(Records\getValue($record));
    
                return ["Property '{$path}' was {$status} with value: {$value}"];
    
            }, $records);
        }, 
        $records, 
        []
    );

    return implode("\n", $lines);
}


function reduce($callback, $tree, $init)
{

    $value = Records\getValue($tree);

    if (is_array($value)) {
        return $callback($init, $tree);
    }

    $recursiveAcc = $callback($init, $tree);

    $children = $value;
    $recursiveAcc = array_reduce(
        $children,
        fn($acc, $child) => reduce($callback, $child, $acc),
        $recursiveAcc
    );

    return $recursiveAcc;
}