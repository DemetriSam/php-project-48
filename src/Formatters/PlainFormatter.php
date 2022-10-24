<?php

namespace Differ\Differ\PlainFormatter;

use Differ\Differ;

use function Functional\pick;

function render(array $tree)
{
    $lines = collectLines($tree);
    return implode("\n", $lines);
}

function collectLines(array $node, array $lines = [], array $path = [])
{
    $type = pick($node, 'type');
    $key = pick($node, 'key');
    $currentPath = implode('.', putKeyToPath($path, $key));

    switch ($type) {
        case 'root':
        case 'nested':
            $children = pick($node, 'children');
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
            $renderedValue1 = stringify(pick($node, 'value1'));
            $renderedValue2 = stringify(pick($node, 'value2'));
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
            $value = pick($node, 'value');
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

function putKeyToPath(array $path, string|null $key)
{
    return array_filter(
        array_merge($path, [$key]),
        fn($item) => $item !== null
    );
}

function stringify(mixed $value)
{
    return is_array($value) ? '[complex value]' : Differ\toString($value, false);
}
