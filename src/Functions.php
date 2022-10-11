<?php

namespace Differ\Differ;

function genDiff(string $path1, string $path2, string $formatName = 'stylish')
{
    $first = parseFile($path1);
    $second = parseFile($path2);

    $diff = prepareDiff($first, $second, $formatName);

    return $diff;
}

function prepareDiff(array $first, array $second, string $formatName)
{
    $diffTree = buildDiffTree($first, $second);
    return render($diffTree, $formatName);
}

function printDiff(string $first, string $second, string $formatName = 'stylish')
{
    echo genDiff($first, $second, $formatName);
}
