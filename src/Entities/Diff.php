<?php

namespace Gen\Diff\Diff;

function makeDiff($key, $old, $actual, $path, $firstArray, $secondArray)
{
    $status = compareThePair($old, $actual, $path, $firstArray, $secondArray);

    return compact('key', 'old', 'actual', 'status', 'path');
}

function compareThePair($first, $second, $path, $firstArray, $secondArray)
{
    $isKeyExistInTheFirstArray = isKeyExistsInDepth($path, $firstArray);
    $isKeyExistInTheSecondArray = isKeyExistsInDepth($path, $secondArray);

    if (!$isKeyExistInTheFirstArray and $isKeyExistInTheSecondArray) {
        return 'added';
    } elseif ($isKeyExistInTheFirstArray and !$isKeyExistInTheSecondArray) {
        return 'removed';
    } elseif ($first === $second) {
        return 'same';
    } else {
        return 'updated';
    }
}

function isKeyExistsInDepth($path, $array)
{
    $key = array_pop($path);
    $root = getValueByPath($array, $path);
    return is_array($root) ? array_key_exists($key, $root) : false;
}

function getValueByPath($array, $path)
{
    foreach ($path as $key) {
        $array = isset($array[$key]) ? $array[$key] : null;
    }

    return $array;
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

function getCurrentPath($diff)
{
    return $diff['path'];
}