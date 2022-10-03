<?php

namespace Gen\Diff;

use Funct\Collection;

function printDiff(string $first, string $second, string $format = 'stylish')
{
    $first = parseFile($first);
    $second = parseFile($second);

    $diff = genDiff($first, $second);

    ksort($diff);
    echo makeStylishString($diff);
}

function genDiff(array $first, array $second)
{
    $keys = array_keys(array_merge($first, $second));

    sort($keys);

    $plucked = array_map(fn($key) => [$key, Collection\pluck([$first, $second], $key)], $keys);

    return array_map(function($diff) {
        [$key, $values] = $diff;
        [$first, $second] = $values;

        if ($first === null and $second !== null) {
            return makeNode($key, $values, 'added');
        } elseif ($first !== null and $second === null) {
            return makeNode($key, $values, 'deleted');
        } elseif ($first === $second) {
            return makeNode($key, $values, 'same');
        } else {
            return makeNode($key, $values, 'changed');
        }        
    }, $plucked);
}