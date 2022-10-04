<?php

namespace Gen\Diff\Diff;

function makeDiff($key, $old, $actual)
{
    $status = compareThePair($old, $actual);

    return compact('key', 'old', 'actual', 'status');
}

function compareThePair($first, $second)
{
    if ($first === null and $second !== null) {
        return 'added';
    } elseif ($first !== null and $second === null) {
        return 'deleted';
    } elseif ($first === $second) {
        return 'same';
    } else {
        return 'changed';
    }   
}

function getOld($diff)
{
    return $diff['old'];
}

function getActual($diff)
{
    return $diff['actual'];
}

function getStatus($diff)
{
    return $diff['status'];
}

function getKey($diff)
{
    return $diff['key'];
}

