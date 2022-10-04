<?php

namespace Gen\Diff;

use Funct\Collection;
use Gen\Diff\Records;

function makeStylishString($records)
{

    return makeString($records);
}

function makeString($input, $replacer = ' ', $spacesCount = 2)
{
    $intend = str_repeat($replacer, $spacesCount);

    $iter = function ($input, $depth) use (&$iter, $intend) {
        if (!is_array($input)) {
            return toString($input);
        }

        $depthIntend = str_repeat($intend, $depth + 1);
        $bracketIntend = str_repeat($intend, $depth);

        $lines = array_map(
            function($record) use ($depthIntend, $bracketIntend, $iter, $depth) {
                $tag = Records\getTag($record);
                $key = Records\getKey($record);
                $value = Records\getValue($record);
                
                
                if(Records\getType($record) === 'node') {
                    return "{$bracketIntend}{$tag} {$key}: {$iter($value, $depth + 1)}";    
                }

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

function stringify($input, $replacer = ' ', $spacesCount = 1)
{
    $intend = str_repeat($replacer, $spacesCount);

    $iter = function ($input, $depth) use (&$iter, $intend) {
        if (!is_array($input)) {
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