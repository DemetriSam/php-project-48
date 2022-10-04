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
    $records = record($keysCommonTree, $first, $second);
    $string = makeString($records);

    return $string;
}



function buildDictOfValues($keysCommonTree, $first, $second)
{

    $iter = function($node, $path = []) use (&$iter, $first, $second) {

    };
    $dictOfValues = array_reduce($keysCommonTree, function($carry, $node) use ($first, $second) {

    });
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

function getType($node)
{
    return $node['type'];
}

function getKey($node)
{
    return $node['key'];
}