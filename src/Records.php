<?php

namespace Gen\Diff;

function makeRecords($node)
{
    $key = getKey($node);
    $old = getOld($node);
    $actual = getActual($node);
    $nodeStatus = getStatus($node);

    switch ($nodeStatus) {
        case 'added': return [
            [
                'record' => $actual,
                'status' => 'actual',
                'tag' => '+',
                ...compact('key', 'nodeStatus'),
            ]
        ];
        
        case 'deleted': return [
            [
                'record' => $old,
                'status' => 'old',
                'tag' => '-',
                ...compact('key', 'nodeStatus'),
            ]
        ];

        case 'same': return [
            [
                'record' => $actual,
                'status' => 'actual',
                'tag' => ' ',
                ...compact('key', 'nodeStatus'),
            ]
        ];

        case 'changed': return [
            [
                'record' => $old,
                'status' => 'old',
                'tag' => '-',
                ...compact('key', 'nodeStatus'),
            ],
            [
                'record' => $actual,
                'status' => 'actual',
                'tag' => '+',
                ...compact('key', 'nodeStatus'),
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