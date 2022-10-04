<?php

namespace Gen\Diff;

use Funct\Collection;
use Gen\Diff\Diff;
use Gen\Diff\Records;

function record($tree, $first, $second)
{
    $tree = Collection\sortBy($tree, fn($node) => getKey($node));
    
    $records = array_reduce($tree, function($records, $node) use ($first, $second) {
        $type = getType($node);
        $key = getKey($node);

        if($type === 'leaf') {
            $firstValue = isset($first[$key]) ? $first[$key]: null;
            $secondValue = isset($second[$key]) ? $second[$key]: null;

            $diff = Diff\makeDiff($key, $firstValue, $secondValue);
            $records = array_merge($records, Records\makeRecords($diff));
        }

        return $records;
    }, []);

    return $records;
}