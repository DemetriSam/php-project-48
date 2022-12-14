<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseData(string $data, string $format): array
{
    switch ($format) {
        case 'json':
            return parseJson($data);

        case 'yaml':
        case 'yml':
            return parseYaml($data);

        default:
            throw new \Exception("Format $format is not supported!");
    }
}

function parseJson(string $content): array
{
    return (array) json_decode($content, true);
}

function parseYaml(string $content): array
{
    return (array) Yaml::parse($content);
}
