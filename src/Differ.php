<?php

namespace Differ\Differ;

use function Differ\Parsers\parseData;
use function Differ\Recorder\buildDiffTree;
use function Differ\Formatters\render;

function genDiff(string $path1, string $path2, string $formatName = 'stylish'): string
{
    $first = parseData(...[getDataFromFile($path1), getFileType($path1)]);
    $second = parseData(...[getDataFromFile($path2), getFileType($path2)]);

    $diffTree = buildDiffTree($first, $second);
    $diff = render($diffTree, $formatName);

    return $diff;
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
