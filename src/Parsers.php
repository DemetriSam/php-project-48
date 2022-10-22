<?php

namespace Differ\Differ;

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

function parseJson(string $content)
{
    return (array) json_decode($content, true);
}

function parseYaml(string $content)
{
    return (array) Yaml::parse($content);
}
