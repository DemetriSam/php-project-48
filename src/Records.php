<?php

namespace Gen\Diff\Records;

use Gen\Diff\Diff;

function makeRecords($diff, $path)
{
    $key = Diff\getKey($diff);
    $old = Diff\getOld($diff);
    $actual = Diff\getActual($diff);
    $diffStatus = Diff\getStatus($diff);

    switch ($diffStatus) {
        case 'added': return [
            [
                ...compact('key', 'diffStatus', 'path'),
                'record' => $actual,
                'status' => 'actual',
                'tag' => '+',
                
            ]
        ];
        
        case 'deleted': return [
            [
                ...compact('key', 'diffStatus', 'path'),
                'record' => $old,
                'status' => 'old',
                'tag' => '-',
                
            ]
        ];

        case 'same': return [
            [
                ...compact('key', 'diffStatus', 'path'),
                'record' => $actual,
                'status' => 'actual',
                'tag' => ' ',
                
            ]
        ];

        case 'changed': return [
            [
                ...compact('key', 'diffStatus', 'path'),
                'record' => $old,
                'status' => 'old',
                'tag' => '-',
                
            ],
            [
                ...compact('key', 'diffStatus', 'path'),
                'record' => $actual,
                'status' => 'actual',
                'tag' => '+',
                
            ],
        ];
    }
}

function makeParentRecord($childRecords, $key, $diffStatus, $path)
{
    if($diffStatus === 'same') {
        $tag = ' ';
    } elseif($diffStatus === 'added') {
        $tag = '+';
    } elseif($diffStatus === 'deleted') {
        $tag = ' ';
    }

    return [
        [
            ...compact('key', 'tag', 'diffStatus', 'path'),
            'record' => $childRecords,
            'status' => 'actual',
        ]
    ];
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