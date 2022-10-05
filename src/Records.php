<?php

namespace Gen\Diff\Records;

use Gen\Diff\Diff;

const PLUS = '+ ';
const MINUS = '- ';
const EMPTY_TAG = '  ';

function makeRecords($diff, $path)
{
    $key = Diff\getKey($diff);
    $old = Diff\getOld($diff);
    $actual = Diff\getActual($diff);
    $diffStatus = Diff\getStatus($diff);
    $type = 'leaf';

    switch ($diffStatus) {
        case 'added':
            return makeSingleRecord($key, $actual, $diffStatus);

        case 'deleted':
            return makeSingleRecord($key, $old, $diffStatus);

        case 'same':
            return makeSingleRecord($key, $actual, $diffStatus);

        case 'changed':
            return makePairRecord($key, $old, $actual, $diffStatus);
    }
}

function makeParentRecord($childRecords, $key, $diffStatus, $path)
{
    if ($diffStatus === 'same') {
        $tag = EMPTY_TAG;
        $status = 'actual';
    } elseif ($diffStatus === 'added') {
        $tag = PLUS;
        $status = 'actual';
    } elseif ($diffStatus === 'deleted') {
        $tag = MINUS;
        $status = 'old';
    }

    return [
        [
            ...compact('key', 'tag', 'diffStatus', 'path'),
            'type' => 'node',
            'record' => $childRecords,
        ]
    ];
}

function makeRecordsWithoutCompare($tree)
{
    return array_map(
        function ($key, $value) {
            $tag = EMPTY_TAG;
            $status = 'not_compared';

            if (!is_array($value)) {
                $record = $value;
                $type = 'leaf';
            } else {
                $record = makeRecordsWithoutCompare($value);
                $type = 'node';
            }

            return compact('key', 'record', 'type', 'tag');
        },
        array_keys($tree),
        $tree
    );
}

function makeSingleRecord($key, $value, $diffStatus)
{
    if ($diffStatus === 'same') {
        $tag = EMPTY_TAG;
        $status = 'actual';
    } elseif ($diffStatus === 'added') {
        $tag = PLUS;
        $status = 'actual';
    } elseif ($diffStatus === 'deleted') {
        $tag = MINUS;
        $status = 'old';
    }

    $type = 'leaf';
    $record = $value;

    return [compact('key', 'diffStatus', 'type', 'record', 'tag', 'status')];
}

function makePairRecord($key, $old, $actual, $diffStatus)
{
    $first = makeSingleRecord($key, $old, 'deleted');
    $second = makeSingleRecord($key, $actual, 'added');

    return array_merge($first, $second);
}

function getTag($record)
{

    return $record['tag'];
}

function getKey($record)
{
    return $record['key'];
}

function getValue($record)
{
    return $record['record'];
}

function getType($record)
{
    return $record['type'];
}
