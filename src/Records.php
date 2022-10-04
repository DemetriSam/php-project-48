<?php

namespace Gen\Diff\Records;

use Gen\Diff\Diff;

function makeRecords($diff)
{
    $key = Diff\getKey($diff);
    $old = Diff\getOld($diff);
    $actual = Diff\getActual($diff);
    $diffStatus = Diff\getStatus($diff);

    switch ($diffStatus) {
        case 'added': return [
            [
                'record' => $actual,
                'status' => 'actual',
                'tag' => '+',
                ...compact('key', 'diffStatus'),
            ]
        ];
        
        case 'deleted': return [
            [
                'record' => $old,
                'status' => 'old',
                'tag' => '-',
                ...compact('key', 'diffStatus'),
            ]
        ];

        case 'same': return [
            [
                'record' => $actual,
                'status' => 'actual',
                'tag' => ' ',
                ...compact('key', 'diffStatus'),
            ]
        ];

        case 'changed': return [
            [
                'record' => $old,
                'status' => 'old',
                'tag' => '-',
                ...compact('key', 'diffStatus'),
            ],
            [
                'record' => $actual,
                'status' => 'actual',
                'tag' => '+',
                ...compact('key', 'diffStatus'),
            ],
        ];
    }
}


function getTag($record)
{
    return $record['tag'];
}

function getKey($record)
{
    return $record['key'];
}

function getValue($record)
{
    return $record['record'];
}