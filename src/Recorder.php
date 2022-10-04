<?php

namespace Gen\Diff;

use Funct\Collection;
use Gen\Diff\Diff;
use Gen\Diff\Records;

function record($tree, $first, $second)
{
    $iter = function ($tree, $path = []) use (&$iter, $first, $second) {
        

        $tree = Collection\sortBy($tree, fn($node) => getKey($node));

        $records = array_reduce($tree, function($records, $node) use ($iter, $first, $second, $path) {
            
            $key = getKey($node);
            $path[] = $key;

            $firstValue = getValueByPath($first, $path);
            $secondValue = getValueByPath($second, $path);

            $type = getType($node);

            if($type === 'leaf') {
                $diff = Diff\makeDiff($key, $firstValue, $secondValue, $path, $first, $second);
                $records = array_merge($records, Records\makeRecords($diff, $path));
            }
    
            if($type === 'nodeBoth') {
                $childRecords = $iter(getChildren($node), $path);
                $parentRecord = Records\makeParentRecord($childRecords, $key, 'same', $path);

                $records = array_merge($records, $parentRecord);
            }
            
            return $records;
            
        }, []);

        return $records;
    };

    return $iter($tree);
}

function getValueByPath($array, $path)
{
    foreach($path as $key) {
        $array = isset($array[$key]) ? $array[$key] : null;
    }

    return $array;
}
