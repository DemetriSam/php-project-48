<?php

namespace Differ\Differ;

function genDiff(string $first, string $second, string $formatName = 'stylish')
{
    $first = parseFile($first);
    $second = parseFile($second);

    $diff = prepareDiff($first, $second, $formatName);
    
    echo $diff;

    return $diff;
}

function prepareDiff(array $first, array $second, $formatName)
{
    $keysCommonTree = buildKeysCommonTree($first, $second);
    $records = record($keysCommonTree, $first, $second);
    return render($records, $formatName);
}
