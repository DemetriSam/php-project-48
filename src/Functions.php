<?php

namespace Differ\Differ;

function genDiff(string $first, string $second, string $formatName = 'stylish')
{
    $first = parseFile($first);
    $second = parseFile($second);

    $diff = prepareDiff($first, $second, $formatName);

    return $diff;
}

function prepareDiff(array $first, array $second, $formatName)
{
    $diffTree = buildDiffTree($first, $second);
    return render($diffTree, $formatName);
}

function printDiff(string $first, string $second, string $formatName = 'stylish')
{
    //я не понял зачем нужна эта кавычка перед выводом, но тесты просят, чтоб была кавычка
    echo '\'' . genDiff($first, $second, $formatName);
}
