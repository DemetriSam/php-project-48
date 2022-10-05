<?php

namespace Gen\Diff;

function printDiff(string $first, string $second, string $formatName)
{
    $first = parseFile($first);
    $second = parseFile($second);

    echo genDiff($first, $second, $formatName);
}

function genDiff(array $first, array $second, $formatName = 'stylish')
{
    $keysCommonTree = buildKeysCommonTree($first, $second);
    $records = record($keysCommonTree, $first, $second);
    return render($records, $formatName);
}
