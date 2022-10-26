<?php

namespace Differ\Formatters\PlainFormatter;

use Differ\Differ;

use function Functional\pick;

function render(array $tree): string
{
    $lines = collectLines($tree);
    return implode("\n", $lines);
}

function collectLines(array $node, array $lines = [], array $ancestry = []): array
{
    $type = pick($node, 'type');
    $key = pick($node, 'key');
    $path = buildPathToCurrentNode($ancestry, $key);
    $pathString = implode('.', $path);

    switch ($type) {
        case 'root':
        case 'nested':
            $children = pick($node, 'children');
            return array_reduce(
                $children,
                fn($lines, $child) => collectLines(
                    $child,
                    $lines,
                    $path,
                ),
                $lines
            );

        case 'changed':
            $renderedValue1 = stringify(pick($node, 'value1'));
            $renderedValue2 = stringify(pick($node, 'value2'));
            return array_merge(
                $lines,
                ["Property '{$pathString}' was updated. From {$renderedValue1} to {$renderedValue2}"]
            );

        case 'deleted':
            return array_merge(
                $lines,
                ["Property '{$pathString}' was removed"]
            );

        case 'added':
            $value = pick($node, 'value');
            $renderedValue = stringify($value);
            return array_merge(
                $lines,
                ["Property '{$pathString}' was added with value: {$renderedValue}"]
            );

        case 'unchanged':
            return $lines;

        default:
            throw new \Exception("Unknown or not existed state");
    }
}

function buildPathToCurrentNode(array $path, ?string $key): array
{
    return array_filter(
        array_merge($path, [$key]),
        fn($item) => $item !== null
    );
}

function stringify(mixed $value): string
{
    return is_array($value) ? '[complex value]' : toString($value, false);
}

function toString(mixed $input, bool $trim = true): string
{
    $exported = var_export($input, true) === 'NULL' ? 'null' : var_export($input, true);

    return $trim ? trim($exported, "'") : $exported;
}
