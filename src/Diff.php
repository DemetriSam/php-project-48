<?php

namespace Gen\Diff;

function makeDiff($key, $values, $status)
{
    [$old, $actual] = $values;

    return compact('key', 'old', 'actual', 'status');
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