<?php

namespace Differ\Differ;

use Funct\Collection;
use Functional;

function record($tree, $first, $second)
{
    $iter = function ($tree, $path = []) use (&$iter, $first, $second) {


        $tree = Collection\sortBy($tree, fn($node) => getKey($node));

        $records = array_reduce($tree, function ($records, $node) use ($iter, $first, $second, $path) {

            $key = getKey($node);
            $path[] = $key;

            $firstValue = Diff\getValueByPath($first, $path);
            $secondValue = Diff\getValueByPath($second, $path);

            $type = getType($node);

            if ($type === 'leaf') {
                $diff = Diff\makeDiff($key, $firstValue, $secondValue, $path, $first, $second);
                $records = array_merge($records, Records\makeRecordsByDiff($diff));
            }

            if ($type === 'nodeBoth') {
                $childRecords = $iter(getChildren($node), $path);
                $parentRecord = Records\makeParentRecord($childRecords, $key, 'same', $path);

                $records = array_merge($records, $parentRecord);
            }

            if ($type === 'nodeFirst') {
                $childRecords = Records\makeRecordsWithoutCompare($firstValue, $path);

                if (Diff\isKeyExistsInDepth($path, $second)) {
                    $parentRecord = Records\makeParentRecord($childRecords, $key, 'removed', $path, true);
                    $singleRecord = Records\makeSingleRecord($key, $secondValue, 'added', $path, true);
                } else {
                    $parentRecord = Records\makeParentRecord($childRecords, $key, 'removed', $path, false);
                    $singleRecord = [];
                }

                $records = array_merge($records, $parentRecord, $singleRecord);
            }

            if ($type === 'nodeSecond') {
                $childRecords = Records\makeRecordsWithoutCompare($secondValue, $path);

                if (Diff\isKeyExistsInDepth($path, $first)) {
                    $parentRecord = Records\makeParentRecord($childRecords, $key, 'added', $path, true);
                    $singleRecord = Records\makeSingleRecord($key, $firstValue, 'removed', $path, true);
                } else {
                    $parentRecord = Records\makeParentRecord($childRecords, $key, 'added', $path, false);
                    $singleRecord = [];
                }

                $records = array_merge($records, $singleRecord, $parentRecord);
            }

            return $records;
        }, []);

        return $records;
    };

    return $iter($tree);
}

function buildDiff($first, $second)
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

function buildDiffTree($first, $second): array
{
    return [
        'type' => 'root',
        'children' => buildDiff($first, $second),
    ];
}

function getType($node)
{
    return $node['type'];
}

function getChildren($node)
{
    return $node['children'];
}

function getKey($node)
{
    return (getType($node) !== 'root') ? $node['key'] : null;
}

function getValue($node)
{
    if (getType($node) === 'deleted' || getType($node) === 'added' || getType($node) === 'unchanged') {
        return $node['value'];
    }

    if (getType($node) === 'changed') {
        $value1 = $node['value1'];
        $value2 = $node['value2'];

        return [$value1, $value2];
    }

    $type = getType($node);
    $key = ($type === 'root') ? 'root' : getKey($node);
    throw new \Exception("Node '$key' of '$type' type has not value field");
}
