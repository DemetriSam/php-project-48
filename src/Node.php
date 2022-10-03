<?php

namespace Gen\Diff;

function makeNode($key, $values, $status)
{
    [$old, $actual] = $values;

    return compact('key', 'old', 'actual', 'status');
}

function getOld($node)
{
    return $node['old'];
}

function getActual($node)
{
    return $node['actual'];
}

function getStatus($node)
{
    return $node['status'];
}

function getKey($node)
{
    return $node['key'];
}