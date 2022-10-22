<?php

namespace Differ\Differ;

use Functional;

function buildDiff(array $first, array $second)
{
    $keys = array_unique(
        array_merge(
            array_keys($first),
            array_keys($second)
        )
    );

    $sortedKeys = Functional\sort($keys, fn ($left, $right) => strcmp($left, $right));

    return array_map(function ($key) use ($first, $second) {

        $value1 = $first[$key] ?? null;
        $value2 = $second[$key] ?? null;

        if (!array_key_exists($key, $first)) {
            return [
                'key' => $key,
                'type' => 'added',
                'value' => $value2,
            ];
        }

        if (!array_key_exists($key, $second)) {
            return [
                'key' => $key,
                'type' => 'deleted',
                'value' => $value1,
            ];
        }

        if (is_array($value1) && is_array($value2)) {
            return [
                'key' => $key,
                'type' => 'nested',
                'children' => buildDiff($value1, $value2),
            ];
        }

        /** @phpstan-ignore-next-line */
        if (array_key_exists($key, $first) && array_key_exists($key, $second)) {
            if ($value1 === $value2) {
                return [
                    'key' => $key,
                    'type' => 'unchanged',
                    'value' => $value1,
                ];
            }

            return [
                'key' => $key,
                'type' => 'changed',
                'value1' => $value1,
                'value2' => $value2,
            ];
        }
    }, $sortedKeys);
}

function buildDiffTree(array $first, array $second): array
{
    return [
        'type' => 'root',
        'children' => buildDiff($first, $second),
    ];
}

function getType(array $node)
{
    return $node['type'];
}

function getChildren(array $node)
{
    return $node['children'];
}

function getKey(array $node)
{
    return (getType($node) !== 'root') ? $node['key'] : null;
}

function getValue(array $node)
{
    $type = getType($node);

    switch ($type) {
        case 'deleted':
        case 'added':
        case 'unchanged':
            return $node['value'];

        case 'changed':
            $value1 = $node['value1'];
            $value2 = $node['value2'];

            return [$value1, $value2];

        case 'root':
        case 'nested':
            throw new \Exception("A node of the '$type' type has not value field");

        default:
            throw new \Exception("The '$type' type is not supported");
    }
}
