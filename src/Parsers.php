<?php

namespace Gen\Diff;

use Symfony\Component\Yaml\Yaml;

function parseFile($filePath): array
{
    $array = explode('.', $filePath);
    $reversed = array_reverse($array);
    $extension = $reversed[0];

    switch ($extension) {
        case 'json':
            return parseJson($filePath);

        case 'yaml':
            return parseYaml($filePath);

        case 'yml':
            return parseYaml($filePath);

        default:
            throw new \Exception("Format $extension is not supported!");
    }
}

function parseJson($filePath)
{
    return (array) json_decode(file_get_contents($filePath));
}

function parseYaml($filePath)
{
    return (array) Yaml::parse(file_get_contents($filePath), Yaml::PARSE_OBJECT_FOR_MAP);
}
