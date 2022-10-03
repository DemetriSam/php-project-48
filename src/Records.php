<?php

namespace Gen\Diff;

function makeRecords($node)
{
    $key = getKey($node);
    $old = getOld($node);
    $actual = getActual($node);
    $diffStatus = getStatus($node);

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


function getRecordTag($record)
{
    return $record['tag'];
}

function getRecordKey($record)
{
    return $record['key'];
}

function getRecordValue($record)
{
    return $record['record'];
}