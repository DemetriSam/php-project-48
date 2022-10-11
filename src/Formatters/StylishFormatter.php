<?php

namespace Differ\Differ\StylishFormatter;

use Differ\Differ;

const PLUS = '+ ';
const MINUS = '- ';
const EMPTY_TAG = '  ';

function render($node, $replacer = ' ', $spacesCount = 4)
{
    $indent = str_repeat($replacer, $spacesCount);

    $iter = function ($node, $depth) use (&$iter, $indent) {

        $startIndent = '  ';
        $depthIndent = str_repeat($indent, $depth);

        $itemIndent = "{$startIndent}{$depthIndent}";
        $bracketIndent = "{$startIndent}{$startIndent}{$depthIndent}";


        if (Differ\getType($node) === 'changed') {
            $key = Differ\getKey($node);
            $tag1 = MINUS;
            $tag2 = PLUS;

            [$value1, $value2] = Differ\getValue($node);

            $renderedValue1 = stringify($value1, $depth + 1);
            $renderedValue2 = stringify($value2, $depth + 1);

            $first = "{$itemIndent}{$tag1}{$key}: {$renderedValue1}";
            $second = "{$itemIndent}{$tag2}{$key}: {$renderedValue2}";

            return implode("\n", [$first, $second]);
        }

        if (
            Differ\getType($node) === 'deleted' ||
            Differ\getType($node) === 'added' ||
            Differ\getType($node) === 'unchanged'
        ) {
            $tag = getTag($node);
            $key = Differ\getKey($node);
            $value = Differ\getValue($node);

            $renderedValue = stringify($value, $depth + 1);

            return "{$itemIndent}{$tag}{$key}: {$renderedValue}";
        }

        if (Differ\getType($node) === 'root') {
            $children = Differ\getChildren($node);
            $lines = array_map(
                function ($node) use ($itemIndent, $iter, $depth) {
                    $result = $iter($node, $depth);
                    return rtrim($result);
                },
                $children
            );

            $result = ['{', ...$lines, "{$depthIndent}}"];
            return implode("\n", $result);
        }

        if (Differ\getType($node) === 'nested') {
            $key = Differ\getKey($node);
            $children = Differ\getChildren($node);
            $tag = EMPTY_TAG;

            $lines = array_map(
                function ($node) use ($itemIndent, $iter, $depth) {
                    $result = $iter($node, $depth + 1);
                    return rtrim($result);
                },
                $children
            );

            $result = ["{$itemIndent}{$tag}{$key}: {", ...$lines, "{$bracketIndent}}"];
            return implode("\n", $result);
        }

        throw new \Exception("Unknown or not existed state");
    };

    return $iter($node, 0);
}

function stringify($data, $startDepth = 0, $replacer = ' ', $spacesCount = 4)
{
    $intend = str_repeat($replacer, $spacesCount);

    $iter = function ($data, $depth) use (&$iter, $intend) {
        if (!is_array($data)) {
            return Differ\toString($data);
        }

        $depthIntend = str_repeat($intend, $depth + 1);
        $bracketIntend = str_repeat($intend, $depth);

        $lines = array_map(
            fn($key, $value) => "{$depthIntend}{$key}: {$iter($value, $depth + 1)}",
            array_keys($data),
            array_values($data)
        );

        $result = ['{', ...$lines, "{$bracketIntend}}"];
        return implode("\n", $result);
    };

    return $iter($data, $startDepth);
}

function getTag($node)
{
    $tags = [
        'added' => PLUS,
        'deleted' => MINUS,
        'unchanged' => EMPTY_TAG,
    ];

    return($tags[Differ\getType($node)]);
}
