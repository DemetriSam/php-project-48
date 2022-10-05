<?php

namespace Gen\Diff;

use Funct\Collection;
use Gen\Diff\Records;

function makeStylishString($records)
{

    return makeString($records);
}

function makeString($input, $replacer = ' ', $spacesCount = 4)
{
    $indent = str_repeat($replacer, $spacesCount);

    $iter = function ($input, $depth) use (&$iter, $indent) {
        if (!is_array($input)) {
            return toString($input);
        }

        $startIndent = '  ';
        $depthIndent = str_repeat($indent, $depth);

        $itemIndent = "{$startIndent}{$depthIndent}";
        $bracketIndent = str_repeat($indent, $depth);

        $lines = array_map(
            function($record) use ($itemIndent, $iter, $depth) {
                $tag = Records\getTag($record);
                $key = Records\getKey($record);
                $value = Records\getValue($record);
                
                $result = "{$itemIndent}{$tag}{$key}: {$iter($value, $depth + 1)}";
                return rtrim($result);
            },
            $input
        );

        $result = ['{', ...$lines, "{$bracketIndent}}"];
        return implode("\n", $result);
    };

    return $iter($input, 0);
}

function toString($input)
{
    $exported = var_export($input, true) === 'NULL' ? 'null' : var_export($input, true);
    
    return trim($exported, "'");
}

function flattenRecursive($items)
{
    return Collection\flatten($items);
}

function stringify($input, $replacer = ' ', $spacesCount = 1)
{
    $indent = str_repeat($replacer, $spacesCount);

    $iter = function ($input, $depth) use (&$iter, $indent) {
        if (!is_array($input)) {
            return toString($input);
        }

        $depthIndent = str_repeat($indent, $depth + 1);
        $bracketIndent = str_repeat($indent, $depth);

        $lines = array_map(
            fn($key, $value) => "{$depthIndent}{$key}: {$iter($value, $depth + 1)}",
            array_keys($input),
            array_values($input)
        );

        $result = ['{', ...$lines, "{$bracketIndent}}"];
        return implode("\n", $result);
    };

    return $iter($input, 0);
}