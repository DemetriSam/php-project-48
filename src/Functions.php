<?php

namespace Gen\Diff;

use Funct\Collection;

function printDiff(string $first, string $second, string $format = 'stylish')
{
    $first = parseFile($first);
    $second = parseFile($second);

    $diff = genDiff($first, $second);

    ksort($diff);
    echo makeStylishString($diff);
}

function genDiff(array $first, array $second)
{
    $keysCommonTree = buildKeysCommonTree($first, $second);
    $diffTree = buildDiffTree($keysCommonTree, $first, $second);
}

function compareThePair($first, $second)
{
    if ($first === null and $second !== null) {
        return makeDiff($key, $values, 'added');
    } elseif ($first !== null and $second === null) {
        return makeDiff($key, $values, 'deleted');
    } elseif ($first === $second) {
        return makeDiff($key, $values, 'same');
    } else {
        return makeDiff($key, $values, 'changed');
    }   
}

function buildDiffTree($keysCommonTree, $first, $second)
{
    $diffTree = array_map(
        function($node) use ($first, $second) {
            
            $diff = compareThePair();
        },
        $keysCommonTree
    );
}

function buildKeysCommonTree(array $first, array $second = [])
{
    $mergedKeys = array_keys(array_merge($first, $second));

    return array_map(
        function ($key) use ($first, $second) {
            if (isset($first[$key]) and is_array($first[$key]) and isset($second[$key]) and is_array($second[$key])) {
                $children = buildKeysCommonTree($first[$key], $second[$key]);
                $type = 'nodeBoth';
                return compact('key', 'type', 'children');
            } elseif(isset($first[$key]) and is_array($first[$key])) {
                $children = buildKeysCommonTree($first[$key]);
                $type = 'nodeFirst';
                return compact('key', 'type', 'children');
            } elseif(isset($second[$key]) and is_array($second[$key])) {
                $children = buildKeysCommonTree($second[$key]);
                $type = 'nodeSecond';
                return compact('key', 'type', 'children');
            } else {
                $type = 'leaf';
                return compact('key', 'type');
            }
        },
        $mergedKeys
    );
}