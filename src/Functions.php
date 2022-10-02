<?php

namespace Gen\Diff;

use Funct\Collection;

use function cli\line;

const TAB = '  ';
const MINUS = '  - ';
const PLUS = '  + ';

function printDiff(string $first, string $second, string $format = 'stylish')
{
    $first = parseFile($first);
    $second = parseFile($second);

    $diff = genDiff($first, $second);

    ksort($diff);
    echo makeStylishString($diff);
}

function makeStylishString($diff)
{
    if(!is_array($diff)) {
        return $diff;
    }

    $line = function($key, $value) {
        switch (getStatus($value)) {
            case 'added': return [
                [
                    'prefix' => "+ {$key}: ", 
                    'value' => getActual($value),
                ]
            ];
            case 'deleted': return [
                [
                    'prefix' => "- {$key}: ",
                    'value' => getOld($value),
                ]
            ];
            case 'changed': return [
                [
                    'prefix' => "- {$key}: ",
                    'value' => getOld($value),
                ], 
                [
                    'prefix' => "+ {$key}: ",
                    'value' => getActual($value),
                ]
            ];
            case 'same': return  [
                [
                    'prefix' => "  {$key}: ",
                    'value' => getActual($value),
                ]
            ];
        }
    };

    $lines = Collection\flatten(array_map(
        fn($key, $value) => $line($key, $value),
        array_keys($diff),
        $diff
    ));

    $linesInDepth = array_map(
        function($lineInDepth) {
            return makeStylishString($lineInDepth['value']);
        },
        $lines
    );

    return stringify($lines, ' ', 4);
}

function stringify($input, $replacer = ' ', $spacesCount = 1)
{
    $intend = str_repeat($replacer, $spacesCount);

    $iter = function ($input, $depth) use (&$iter, $intend) {
        if (!is_array($input) and !is_object($input)) {
            return toString($input);
        }

        $depthIntend = str_repeat($intend, $depth + 1);
        $bracketIntend = str_repeat($intend, $depth);

        $lines = array_map(
            fn($key, $value) => "{$depthIntend}{$key}: {$iter($value, $depth + 1)}",
            array_keys($input),
            array_values($input)
        );

        $result = ['{', ...$lines, "{$bracketIntend}}"];
        return implode("\n", $result);
    };

    return $iter($input, 0);
}

function toString($input)
{
    return trim(var_export($input, true), "'");
}

function genDiff(array $first, array $second)
{
    $keys = array_keys(array_merge($first, $second));

    $plucked = array_reduce($keys, function ($carry, $key) use ($first, $second) {
            $carry[] = [$key, Collection\pluck([$first, $second], $key)];
            return $carry;
    }, []);

    return array_reduce($plucked, function ($carry, $item) {
            [$key, $value] = $item;
            [$first, $second] = $value;

            if ($first === null and $second !== null) {
                $carry[$key]['diff'] = 'added';
            } elseif ($first !== null and $second === null) {
                $carry[$key]['diff'] = 'deleted';
            } elseif ($first === $second) {
                $carry[$key]['diff'] = 'same';
            } else {
                $carry[$key]['diff'] = 'changed';
            }

            if(is_object($first) and is_object($second)) {
                $carry[$key]['diff'] = 'same';
                $carry[$key]['type'] = 'node';
                $carry[$key]['actual'] = genDiff((array)$first, (array)$second);
            } else {
                $carry[$key]['type'] = 'leaf';
                $carry[$key]['old'] = $first;
                $carry[$key]['actual'] = $second;
            }

            if(is_object($first) or is_object($second)) {
                $carry[$key]['type'] = 'node';
            } else {
                $carry[$key]['type'] = 'leaf';
            }

            return $carry;
    }, []);
}

function getStatus($node)
{
    return $node['diff'];
}

function getType($node)
{
    return $node['type'];
}

function getOld($node)
{
    return $node['old'];
}

function getActual($node)
{
    return $node['actual'];
}

/* Альтернативный вариант функции genDiff. на мой вкус, в императивном стиле выглядит поэлегантнее
function genDiffImperative(array $first, array $second)
{
    $merged = array_merge($first, $second);

    $plucked = [];
    foreach ($merged as $key => $value) {
        $plucked[$key] = Collection\pluck([$merged, $first, $second], $key);
    }

    $maped = [];
    foreach ($plucked as $key => $value) {
        [$merged, $first, $second] = $value;

        $maped[$key] = [
            'old' => $first,
            'actual' => $second
        ];

        if ($first === null and $second !== null) {
            $maped[$key]['diff'] = 'added';
        } elseif ($first !== null and $second === null) {
            $maped[$key]['diff'] = 'deleted';
        } elseif ($first === $second) {
            $maped[$key]['diff'] = 'same';
        } else {
            $maped[$key]['diff'] = 'changed';
        }
    }

    return $maped;
}
*/
