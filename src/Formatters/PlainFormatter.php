<?php

namespace Gen\Diff\PlainFormatter;

use Funct\Collection;
use Gen\Diff\Records;

function render($records)
{

    $callback = function($init, $record) {
        $path = implode('.', Records\getCurrentPath($record));
        $status = Records\getDiffStatus($record);
        $value = is_array(Records\getValue($record)) ?
                '[complex value]' :
                Records\toString(Records\getValue($record));

        $line = ["Property '{$path}' was {$status} with value: {$value}"];

        return array_merge($init, $line);

    };

    $lines = array_map(fn($record) => reduce($callback, $record, []), $records);
    var_export($lines);
    return implode("\n", Collection\flatten($lines));
}

function reduce($callback, $tree, $init)
{

    $value = Records\getValue($tree);

    if (!is_array($value)) {
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