<?php

namespace Gen\Diff;

use Funct\Collection;

use function cli\line;

function printDiff(string $first, string $second, string $format = 'stylish')
{
    $firstArray = (array) json_decode(file_get_contents($first));
    $secondArray = (array) json_decode(file_get_contents($second));

    $old = '-';
    $new = '+';
    $same = ' ';

    $diff = genDiff($firstArray, $secondArray);

    ksort($diff);

    line('{');
    foreach ($diff as $key => $value) {
        $status = $value['diff'];

        switch ($status) {
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
    if ($value === true) {
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

    $plucked = array_reduce(
        array_keys($merged),
        function ($carry, $key) use ($merged, $first, $second) {
            $carry[] = [$key, Collection\pluck([$merged, $first, $second], $key)];
            return $carry;
        },
        []
    );

    return array_reduce(
        $plucked,
        function ($carry, $item) {
            [$key, $value] = $item;
            [$merged, $first, $second] = $value;

            $carry[$key] = [
                'old' => $first,
                'actual' => $second
            ];

            if ($first === null and $second !== null) {
                $carry[$key]['diff'] = 'added';
            } elseif ($first !== null and $second === null) {
                $carry[$key]['diff'] = 'deleted';
            } elseif ($first === $second) {
                $carry[$key]['diff'] = 'same';
            } else {
                $carry[$key]['diff'] = 'changed';
            }

            return $carry;
        },
        []
    );
}

/* Альтернативный вариант функции genDiff. на мой вкус, в императивном стиле выглядит поэлегантнее
function genDiffImperative(array $first, array $second)
{
    $merged = array_merge($first, $second);

    $plucked = [];
    foreach ($merged as $key => $value) {
        $plucked[$key] = Collection\pluck([$merged, $first, $second], $key);
    }

    $maped = [];
    foreach ($plucked as $key => $value) {
        [$merged, $first, $second] = $value;

        $maped[$key] = [
            'old' => $first,
            'actual' => $second
        ];

        if ($first === null and $second !== null) {
            $maped[$key]['diff'] = 'added';
        } elseif ($first !== null and $second === null) {
            $maped[$key]['diff'] = 'deleted';
        } elseif ($first === $second) {
            $maped[$key]['diff'] = 'same';
        } else {
            $maped[$key]['diff'] = 'changed';
        }
    }

    return $maped;
}
*/