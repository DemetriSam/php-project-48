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
    $key = array_pop($path);

    $firstRoot = getValueByPath($firstArray, $path);
    $secondRoot = getValueByPath($secondArray, $path);

    $isKeyExistInTheFirstArray = is_array($firstRoot) ? array_key_exists($key, $firstRoot) : false;
    $isKeyExistInTheSecondArray = is_array($secondRoot) ? array_key_exists($key, $secondRoot) : false;

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

