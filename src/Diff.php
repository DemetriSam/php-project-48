<?php

namespace Gen\Diff\Diff;

use function Gen\Diff\getValueByPath;

function makeDiff($key, $old, $actual, $path, $firstArray, $secondArray)
{
    $status = compareThePair($old, $actual, $path, $firstArray, $secondArray);

    return compact('key', 'old', 'actual', 'status');
}

function compareThePair($first, $second, $path, $firstArray, $secondArray)
{
    $isKeyExistInTheFirstArray = is_key_exists_in_depth($path, $firstArray);
    $isKeyExistInTheSecondArray = is_key_exists_in_depth($path, $secondArray);

    if (!$isKeyExistInTheFirstArray and $isKeyExistInTheSecondArray) {
        return 'added';
    } elseif ($isKeyExistInTheFirstArray and !$isKeyExistInTheSecondArray) {
        return 'deleted';
    } elseif ($first === $second) {
        return 'same';
    } else {
        return 'changed';
    }   
}

function is_key_exists_in_depth($path, $array)
{
    $key = array_pop($path);
    $root = getValueByPath($array, $path);
    return is_array($root) ? array_key_exists($key, $root) : false;
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

