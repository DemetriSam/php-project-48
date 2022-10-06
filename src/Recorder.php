<?php

namespace Gen\Diff;

use Funct\Collection;
use Gen\Diff\Diff;
use Gen\Diff\Records;

function record($tree, $first, $second)
{
    $iter = function ($tree, $path = []) use (&$iter, $first, $second) {


        $tree = Collection\sortBy($tree, fn($node) => getKey($node));

        $records = array_reduce($tree, function ($records, $node) use ($iter, $first, $second, $path) {

            $key = getKey($node);
            $path[] = $key;

            $firstValue = Diff\getValueByPath($first, $path);
            $secondValue = Diff\getValueByPath($second, $path);

            $type = getType($node);

            if ($type === 'leaf') {
                $diff = Diff\makeDiff($key, $firstValue, $secondValue, $path, $first, $second);
                $records = array_merge($records, Records\makeRecordsByDiff($diff));
            }

            if ($type === 'nodeBoth') {
                $childRecords = $iter(getChildren($node), $path);
                $parentRecord = Records\makeParentRecord($childRecords, $key, 'same', $path);

                $records = array_merge($records, $parentRecord);
            }

            if ($type === 'nodeFirst') {
                $childRecords = Records\makeRecordsWithoutCompare($firstValue);
                $parentRecord = Records\makeParentRecord($childRecords, $key, 'removed', $path);

                $singleRecord = Diff\isKeyExistsInDepth($path, $second) ?
                                Records\makeSingleRecord($key, $secondValue, 'added', $path) :
                                [];

                $records = array_merge($records, $parentRecord, $singleRecord);
            }

            if ($type === 'nodeSecond') {
                $singleRecord = Diff\isKeyExistsInDepth($path, $first) ?
                                Records\makeSingleRecord($key, $firstValue, 'removed', $path) :
                                [];

                $childRecords = Records\makeRecordsWithoutCompare($secondValue);
                $parentRecord = Records\makeParentRecord($childRecords, $key, 'added', $path);

                $records = array_merge($records, $singleRecord, $parentRecord);
            }

            return $records;
        }, []);

        return $records;
    };

    return $iter($tree);
}
