<?php

namespace Gen\Diff;

use Funct\Collection;

use function cli\line;

const TAB = '    ';
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
    ksort($diff);

    $fieldsString = array_reduce(array_keys($diff), function($carry, $key) use ($diff) {
        $status = $diff[$key]['diff'];
        switch ($status) {
            case 'added':
                return $carry . TAB . "+ {$key}: " . prettyTypes($diff[$key]['actual']) . "\n";
                break;
            case 'deleted':
                return $carry . TAB . "- {$key}: " . prettyTypes($diff[$key]['old']) . "\n";
                break;
            case 'changed':
                return $carry . TAB . "- {$key}: " . prettyTypes($diff[$key]['old']) . "\n" . 
                                TAB . "+ {$key}: " . prettyTypes($diff[$key]['actual']) . "\n";
                break;
            case 'same':
                return $carry . TAB . "  {$key}: " . prettyTypes($diff[$key]['actual']) . "\n";
                break;
        }
    }, '');

    return "{\n$fieldsString}\n";
}

function toString($input)
{
    return trim(var_export($input, true), "'");
}

function stringify($input, $replacer = ' ', $spacesCount = 1)
{

    $intend = str_repeat($replacer, $spacesCount);
    $iter = function ($array, $depth) use (&$iter, $intend) {
        $result = "{\n";
        $depthIntend = str_repeat($intend, $depth + 1);
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $result .= $depthIntend . toString($key) . ': ' . $iter($value, $depth + 1) . $depthIntend . "}\n";
            } else {
                $result .= $depthIntend . toString($key) . ': ' . toString($value) . "\n";
            }
        }
        return $result;
    };

    if (is_array($input)) {
        return $iter($input, 0) . '}';
    }

    return toString($input);
}

function stringifyT($value, string $replacer = ' ', int $spacesCount = 1): string
{
    $iter = function ($currentValue, $depth) use (&$iter, $replacer, $spacesCount) {
        if (!is_array($currentValue)) {
            return toString($currentValue);
        }

        $indentSize = $depth * $spacesCount;
        $currentIndent = str_repeat($replacer, $indentSize);
        $bracketIndent = str_repeat($replacer, $indentSize - $spacesCount);

        $lines = array_map(
            fn($key, $val) => "{$currentIndent}{$key}: {$iter($val, $depth + 1)}",
            array_keys($currentValue),
            $currentValue
        );

        $result = ['{', ...$lines, "{$bracketIndent}}"];

        return implode("\n", $result);
    };

    return $iter($value, 1);
}

function printDiffInTerminal($diff)
{
    $old = '-';
    $new = '+';
    $same = ' ';

    line('{');
    foreach ($diff as $key => $value) {
        $status = $value['diff'];
        switch ($status) {
            case 'added':
                line(" %s %s: %s", $new, $key, prettyTypes($value['actual']));
                break;
            case 'deleted':
                line(" %s %s: %s", $old, $key, prettyTypes($value['old']));
                break;
            case 'changed':
                line(" %s %s: %s", $old, $key, prettyTypes($value['old']));
                line(" %s %s: %s", $new, $key, prettyTypes($value['actual']));
                break;
            case 'same':
                line(" %s %s: %s", $same, $key, prettyTypes($value['actual']));
                break;
        }
    }
    line('}');
}

function prettyTypes($value)
{
    if ($value === true) {
        return 'true';
    } elseif ($value === false) {
        return 'false';
    } else {
        return $value;
    }
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

            $carry[$key] = [
                'old' => $first,
                'actual' => $second
            ];

            if ($first === null and $second !== null) {
                $carry[$key]['diff'] = 'added';
            } elseif ($first !== null and $second === null) {
                $carry[$key]['diff'] = 'deleted';
            } elseif ($first === $second) {
                $carry[$key]['diff'] = 'same';
            } else {
                $carry[$key]['diff'] = 'changed';
            }

            return $carry;
    }, []);
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
