<?php

namespace Gen\Diff\Records;

use Gen\Diff\Diff;

const PLUS = '+ ';
const MINUS = '- ';
const EMPTY_TAG = '  ';

function makeSingleRecord($key, $value, $diffStatus, $path)
{
    if ($diffStatus === 'same') {
        $tag = EMPTY_TAG;
        $status = 'actual';
    } elseif ($diffStatus === 'added') {
        $tag = PLUS;
        $status = 'actual';
    } elseif ($diffStatus === 'removed') {
        $tag = MINUS;
        $status = 'old';
    }

    $type = 'leaf';
    $record = $value;

    return [compact('key', 'diffStatus', 'type', 'record', 'tag', 'status', 'path')];
}

function makePairRecord($key, $old, $actual, $diffStatus, $path)
{
    $first = makeSingleRecord($key, $old, 'removed', $path);
    $second = makeSingleRecord($key, $actual, 'added', $path);

    return array_merge($first, $second);
}

function makeRecordsByDiff($diff)
{
    $key = Diff\getKey($diff);
    $old = Diff\getOld($diff);
    $actual = Diff\getActual($diff);
    $diffStatus = Diff\getStatus($diff);
    $path = Diff\getCurrentPath($diff);

    switch ($diffStatus) {
        case 'added':
            return makeSingleRecord($key, $actual, $diffStatus, $path);

        case 'removed':
            return makeSingleRecord($key, $old, $diffStatus, $path);

        case 'same':
            return makeSingleRecord($key, $actual, $diffStatus, $path);

        case 'updated':
            return makePairRecord($key, $old, $actual, $diffStatus, $path);
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
    } elseif ($diffStatus === 'removed') {
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

function toString($input)
{
    $exported = var_export($input, true) === 'NULL' ? 'null' : var_export($input, true);

    return trim($exported, "'");
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

function getCurrentPath($record)
{
    return $record['path'];
}