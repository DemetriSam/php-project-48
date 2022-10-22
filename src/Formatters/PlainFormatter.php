<?php

namespace Differ\Differ\PlainFormatter;

use Differ\Differ;

function render(array $tree)
{
    $lines = collectLines($tree);
    return implode("\n", $lines);
}

function collectLines(array $node, array $lines = [], array $path = [])
{
    $type = Differ\getType($node);
    $key = Differ\getKey($node);
    $currentPath = implode('.', putKeyToPath($path, $key));

    switch ($type) {
        case 'root':
        case 'nested':
            $children = Differ\getChildren($node);
            return array_reduce(
                $children,
                fn($lines, $child) => collectLines(
                    $child,
                    $lines,
                    putKeyToPath($path, $key),
                ),
                $lines
            );

        case 'changed':
            [$value1, $value2] = Differ\getValue($node);
            $renderedValue1 = stringify($value1);
            $renderedValue2 = stringify($value2);
            return array_merge(
                $lines,
                ["Property '{$currentPath}' was updated. From {$renderedValue1} to {$renderedValue2}"]
            );

        case 'deleted':
            return array_merge(
                $lines,
                ["Property '{$currentPath}' was removed"]
            );

        case 'added':
            $value = Differ\getValue($node);
            $renderedValue = stringify($value);
            return array_merge(
                $lines,
                ["Property '{$currentPath}' was added with value: {$renderedValue}"]
            );

        case 'unchanged':
            return $lines;

        default:
            throw new \Exception("Unknown or not existed state");
    }
}

function putKeyToPath($path, $key)
{
    return array_filter(
        array_merge($path, [$key]),
        fn($item) => $item !== null
    );
}

function stringify($value)
{
    return is_array($value) ? '[complex value]' : Differ\toString($value, false);
}
