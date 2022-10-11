<?php

namespace Differ\Differ\StylishFormatter;

use Differ\Differ\Records;
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
        $bracketIndent = str_repeat($indent, $depth);


        if(Differ\getType($node) === 'changed') {
            return;
        }

        if(Differ\getType($node) === 'deleted') {
            return;
        }

        if(Differ\getType($node) === 'added') {
            return;
        }

        if(Differ\getType($node) === 'unchanged') {
            return;
        }     

        if(Differ\getType($node) === 'root') {
            $children = Differ\getChildren($node); 
            $lines = array_map(
                function ($node) use ($itemIndent, $iter, $depth) {
                    $result = $iter($node, $depth + 1);
                    return rtrim($result);
                },
                $children
            );

            $result = ['{', ...$lines, "{$bracketIndent}}"];
            return implode("\n", $result);
        }

        if(Differ\getType($node) === 'nested') {
            $key = Differ\getKey($node);
            $children = Differ\getChildren($node);
            $tag = EMPTY_TAG;

            $lines = array_map( 
                function($node) use ($itemIndent, $iter, $depth) {
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
            return toString($data);
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

    return $iter($input, $startDepth);
}

function toString($input, $trim = true)
{
    $exported = var_export($input, true) === 'NULL' ? 'null' : var_export($input, true);

    return $trim ? trim($exported, "'") : $exported;
}