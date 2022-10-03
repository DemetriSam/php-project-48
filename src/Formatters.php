<?php

namespace Gen\Diff;

use Funct\Collection;

function makeStylishString($diff)
{
    $records = array_map(fn($node) => makeRecords($node), $diff);
    $flatten = flattenRecursive($records);

    return stringify($flatten);
}

function stringify($input, $replacer = ' ', $spacesCount = 2)
{
    $intend = str_repeat($replacer, $spacesCount);

    $iter = function ($input, $depth) use (&$iter, $intend) {
        if (!is_array($input) and !is_object($input)) {
            return toString($input);
        }

        $depthIntend = str_repeat($intend, $depth + 1);
        $bracketIntend = str_repeat($intend, $depth);

        $lines = array_map(
            function($record) use ($depthIntend, $iter, $depth) {
                $tag = getRecordTag($record);
                $key = getRecordKey($record);
                $value = getRecordValue($record);
                
                return "{$depthIntend}{$tag} {$key}: {$iter($value, $depth + 1)}";
            },
            $input
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

function flattenRecursive($items)
{
    return Collection\flatten($items);
}