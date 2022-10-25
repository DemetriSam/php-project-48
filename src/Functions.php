<?php

namespace Differ\Differ;

function genDiff(string $path1, string $path2, string $formatName = 'stylish')
{
    $first = parseData(...[getDataFromFile($path1), getFileType($path1)]);
    $second = parseData(...[getDataFromFile($path2), getFileType($path1)]);

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

function getDataFromFile(string $filePath): string
{
    $data = file_get_contents($filePath);

    if (!is_string($data)) {
        return '';
    }

    return $data;
}

function getFileType(string $pathToFile): string
{
    return pathinfo($pathToFile, PATHINFO_EXTENSION);
}
