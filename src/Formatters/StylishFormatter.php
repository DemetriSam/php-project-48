<?php

namespace Differ\Differ\StylishFormatter;

use Differ\Differ;

const PLUS = '+ ';
const MINUS = '- ';
const EMPTY_TAG = '  ';
const LENGTH_OF_TAGS = 2;

function render(array $node)
{
    $iter = function ($node, $depth) use (&$iter) {

        $itemIndent = buildIndent($depth, LENGTH_OF_TAGS);
        $bracketIndent = buildIndent($depth);

        $type = Differ\getType($node);
        $tag = getTag($node);

        switch ($type) {
            case 'root':
                $children = Differ\getChildren($node);
                $lines = array_map(
                    function ($node) use ($iter, $depth) {
                        return $iter($node, $depth);
                    },
                    $children
                );

                $result = ['{', ...$lines, '}'];
                return implode("\n", $result);

            case 'nested':
                $key = Differ\getKey($node);
                $children = Differ\getChildren($node);

                $lines = array_map(
                    function ($node) use ($iter, $depth) {
                        return $iter($node, $depth + 1);
                    },
                    $children
                );

                $result = ["{$itemIndent}{$tag}{$key}: {", ...$lines, "{$bracketIndent}}"];
                return implode("\n", $result);

            case 'changed':
                $key = Differ\getKey($node);

                [$tag1, $tag2] = $tag;
                [$value1, $value2] = Differ\getValue($node);

                $renderedValue1 = stringify($value1, $depth + 1);
                $renderedValue2 = stringify($value2, $depth + 1);

                $first = "{$itemIndent}{$tag1}{$key}: {$renderedValue1}";
                $second = "{$itemIndent}{$tag2}{$key}: {$renderedValue2}";

                return implode("\n", [$first, $second]);

            case 'deleted':
            case 'added':
            case 'unchanged':
                $key = Differ\getKey($node);
                $value = Differ\getValue($node);

                $renderedValue = stringify($value, $depth + 1);

                return "{$itemIndent}{$tag}{$key}: {$renderedValue}";

            default:
                throw new \Exception("Unknown or not existed state");
        }
    };

    return $iter($node, 0);
}

function stringify(mixed $data, int $startDepth = 0)
{

    $iter = function ($data, $depth) use (&$iter) {
        if (!is_array($data)) {
            return Differ\toString($data);
        }

        $itemIndent = buildIndent($depth);
        $bracketIndent = buildIndent($depth - 1);

        $lines = array_map(
            fn($key, $value) => "{$itemIndent}{$key}: {$iter($value, $depth + 1)}",
            array_keys($data),
            array_values($data)
        );

        $result = ['{', ...$lines, "{$bracketIndent}}"];
        return implode("\n", $result);
    };

    return $iter($data, $startDepth);
}

function getTag(array $node)
{
    $tags = [
        'added' => PLUS,
        'deleted' => MINUS,
        'unchanged' => EMPTY_TAG,
        'nested' => EMPTY_TAG,
        'changed' => [MINUS, PLUS],
        'root' => 'no tag',
    ];

    return($tags[Differ\getType($node)]);
}

function buildIndent(int $depthOfNode, int $length_of_tag = 0, string $replacer = ' ', int $spaceCount = 4)
{
    $depthOfElement = $depthOfNode + 1;
    return str_repeat($replacer, $spaceCount * $depthOfElement - $length_of_tag);
}
