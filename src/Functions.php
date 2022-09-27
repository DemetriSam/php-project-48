<?php

namespace Gen\Diff;

use function cli\line;
use Funct\Collection;

function getMan() {
    return <<<DOC
gendiff -h

Generate diff

Usage:
    gendiff (-h|--help)
    gendiff (-v|--version)
    gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
    -h --help                     Show this screen
    -v --version                  Show version
    --format <fmt>                Report format [default: stylish]
DOC;
}

function printDiff(string $first, string $second, string $format = 'stylish')
{
    $first = (array) json_decode(file_get_contents($first));
    $second = (array) json_decode(file_get_contents($second));

    $old = '-';
    $new = '+';
    $same = ' ';

    $diff = genDiff($first, $second);
    
    line('{');
    foreach($diff as $key => $value) {
        $status = $value['diff'];
        
        switch($status) {
            case 'added':
                line(" %s %s: %s", $new, $key, prettyTypes($value['actual']));
                break;
            case 'deleted':
                line(" %s %s: %s", $old, $key, prettyTypes($value['old']));
                break;
            case 'changed':                
                line(" %s %s: %s", $old, $key, prettyTypes($value['old']));
                line(" %s %s: %s", $new, $key, prettyTypes($value['actual']));
                break;
            case 'same':
                line(" %s %s: %s", $same, $key, prettyTypes($value['actual']));
                break;
        }
    }
    line('}');
}

function prettyTypes($value)
{
    if($value === true) {
        return 'true';
    } elseif ($value === false) {
        return 'false';
    } else {
        return $value;
    }
}

function genDiff(array $first, array $second)
{
    $merged = array_merge($first, $second);
    
    $plucked = [];
    foreach($merged as $key => $value) {
        $plucked[$key] = Collection\pluck([$merged, $first, $second], $key);
    }

    $maped = [];
    foreach($plucked as $key => $value) {
        [$merged, $first, $second] = $value;
        
        $maped[$key] = [
            'old' => $first,
            'actual' => $second
        ];
        
        if ($first === null and $second !== null) {
            $maped[$key]['diff'] = 'added';
        } elseif($first !== null and $second === null) {
            $maped[$key]['diff'] = 'deleted';
        } elseif ($first === $second) {
            $maped[$key]['diff'] = 'same';
        } else {
            $maped[$key]['diff'] = 'changed';
        }
    }

    return $maped;
}
