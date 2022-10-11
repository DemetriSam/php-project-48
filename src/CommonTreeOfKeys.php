<?php

namespace Differ\Differ;

use Funct\Collection;

function buildKeysCommonTree(array $first, array $second = [])
{
    $mergedKeys = array_keys(array_merge($first, $second));

    return array_map(
        function ($key) use ($first, $second) {
            if (isset($first[$key]) and is_array($first[$key]) and isset($second[$key]) and is_array($second[$key])) {
                $children = buildKeysCommonTree($first[$key], $second[$key]);
                $type = 'nodeBoth';
                return compact('key', 'type', 'children');
            } elseif (isset($first[$key]) and is_array($first[$key])) {
                $children = buildKeysCommonTree($first[$key]);
                $type = 'nodeFirst';
                return compact('key', 'type', 'children');
            } elseif (isset($second[$key]) and is_array($second[$key])) {
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
