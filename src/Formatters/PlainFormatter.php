<?php

namespace Differ\Differ\PlainFormatter;

use Differ\Differ;

function render(array $tree)
{
    $callback = function ($init, $node, $path) {

        $type = Differ\getType($node);
        $key = Differ\getKey($node);
        $currentPath = implode('.', array_merge($path, [$key]));

        if ($type === 'changed') {
            [$value1, $value2] = Differ\getValue($node);

            $renderedValue1 = is_array($value1) ? '[complex value]' : Differ\toString($value1, false);
            $renderedValue2 = is_array($value2) ? '[complex value]' : Differ\toString($value2, false);

            return array_merge(
                $init,
                ["Property '{$currentPath}' was updated. From {$renderedValue1} to {$renderedValue2}"]
            );
        }

        if ($type === 'deleted') {
            return array_merge($init, ["Property '{$currentPath}' was removed"]);
        }

        if ($type === 'added') {
            $value = Differ\getValue($node);
            $renderedValue = is_array($value) ? '[complex value]' : Differ\toString($value, false);

            return array_merge($init, ["Property '{$currentPath}' was added with value: {$renderedValue}"]);
        }

        return $init;
    };

    $lines = reduce($callback, $tree, []);
    return implode("\n", $lines);
}

function reduce(callable $callback, array $tree, array $init, $path = [])
{
    $type = Differ\getType($tree);
    $key = Differ\getKey($tree);

    if ($type === 'nested' or $type === 'root') {
        $recursiveAcc = $callback($init, $tree, $path);
        $children = Differ\getChildren($tree);

        return array_reduce(
            $children,
            fn($acc, $child) => reduce(
                $callback,
                $child,
                $acc,
                array_filter(
                    array_merge($path, [$key]),
                    fn($item) => $item !== null
                )
            ),
            $recursiveAcc
        );
    }

    return $callback($init, $tree, $path);
}
