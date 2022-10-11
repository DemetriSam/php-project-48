<?php

namespace Differ\Differ;

use Symfony\Component\Yaml\Yaml;

function parseFile($filePath): array
{
    $array = explode('.', $filePath);
    $reversed = array_reverse($array);
    $extension = $reversed[0];

    $content = file_get_contents($filePath);

    switch ($extension) {
        case 'json':
            return parseJson($filePath, $content);

        case 'yaml':
            return parseYaml($filePath, $content);

        case 'yml':
            return parseYaml($filePath, $content);

        default:
            throw new \Exception("Format $extension is not supported!");
    }
}

function parseJson($filePath, $content)
{
    return (array) json_decode($content, true);
}

function parseYaml($filePath, $content)
{
    return (array) Yaml::parse($content);
}
