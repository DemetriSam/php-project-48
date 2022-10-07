<?php

namespace Gen\Diff\PlainFormatter;

use Funct\Collection;
use Gen\Diff\Records;

function render($records)
{

    $callback = function($init, $record) {

        if(Records\getDiffStatus($record) === 'not_compared') {
            return $init;
        }
        
        if(Records\isUpdated($record)) {
            return array_merge($init, [$record]);
        }

        $path = implode('.', Records\getCurrentPath($record));
        $status = Records\getDiffStatus($record);
        $value = is_array(Records\getValue($record)) ?
                '[complex value]' :
                Records\toString(Records\getValue($record), false);

        if ($status !== 'same') {
            $line = ($status === 'removed') ? 
                    ["Property '{$path}' was {$status}"] :
                    ["Property '{$path}' was {$status} with value: {$value}"];
        } else {
            $line = [];
        }

        return array_merge($init, $line);

    };

    $lines_raw = Collection\flatten(array_map(fn($record) => reduce($callback, $record, []), $records));

    $lines_without_updated = array_filter($lines_raw, fn($item) => !is_array($item));
    $updated_records = array_filter($lines_raw, fn($item) => is_array($item));
    $groupedByPath = Collection\groupBy($updated_records, fn($record) => implode('.', Records\getCurrentPath($record)));

    $updated_lines = array_map(function($key, $item) {
        $path = $key;
        $norm = array_values($item);
        $old = is_array(Records\getValue($norm[0])) ? 
            '[complex value]' :        
            Records\toString(Records\getValue($norm[0]), false);
        $actual = is_array(Records\getValue($norm[1])) ? 
                '[complex value]' :        
                Records\toString(Records\getValue($norm[1]), false);

        return "Property '{$path}' was updated. From {$old} to {$actual}";
    }, array_keys($groupedByPath), $groupedByPath);

    $lines = array_merge($lines_without_updated, $updated_lines);
    sort($lines);
    return implode("\n", $lines);
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
