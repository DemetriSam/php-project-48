<?php

namespace Differ\Differ\StylishFormatter;

use Differ\Differ\Records;

function render($input, $replacer = ' ', $spacesCount = 4)
{
    $indent = str_repeat($replacer, $spacesCount);

    $iter = function ($input, $depth) use (&$iter, $indent) {
        if (!is_array($input)) {
            return Records\toString($input);
        }

        $startIndent = '  ';
        $depthIndent = str_repeat($indent, $depth);

        $itemIndent = "{$startIndent}{$depthIndent}";
        $bracketIndent = str_repeat($indent, $depth);

        $lines = array_map(
            function ($record) use ($itemIndent, $iter, $depth) {
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
