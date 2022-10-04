<?php

namespace Gen\Diff\Records;

use Gen\Diff\Diff;

const PLUS = '  +';
const MINUS = '  -';
const EMPTY_TAG = '   ';

function makeRecords($diff, $path)
{
    $key = Diff\getKey($diff);
    $old = Diff\getOld($diff);
    $actual = Diff\getActual($diff);
    $diffStatus = Diff\getStatus($diff);
    $type = 'leaf';

    switch ($diffStatus) {
        case 'added': return [
            [
                ...compact('key', 'diffStatus', 'path', 'type'),
                'record' => $actual,
                'status' => 'actual',
                'tag' => PLUS,
                
            ]
        ];
        
        case 'deleted': return [
            [
                ...compact('key', 'diffStatus', 'path', 'type'),
                'record' => $old,
                'status' => 'old',
                'tag' => MINUS,
                
            ]
        ];

        case 'same': return [
            [
                ...compact('key', 'diffStatus', 'path', 'type'),
                'record' => $actual,
                'status' => 'actual',
                'tag' => EMPTY_TAG,
                
            ]
        ];

        case 'changed': return [
            [
                ...compact('key', 'diffStatus', 'path', 'type'),
                'record' => $old,
                'status' => 'old',
                'tag' => MINUS,
                
            ],
            [
                ...compact('key', 'diffStatus', 'path', 'type'),
                'record' => $actual,
                'status' => 'actual',
                'tag' => PLUS,
                
            ],
        ];
    }
}

function makeParentRecord($childRecords, $key, $diffStatus, $path)
{
    if($diffStatus === 'same') {
        $tag = EMPTY_TAG;
    } elseif($diffStatus === 'added') {
        $tag = PLUS;
    } elseif($diffStatus === 'deleted') {
        $tag = EMPTY_TAG;
    }

    return [
        [
            ...compact('key', 'tag', 'diffStatus', 'path'),
            'type' => 'node',
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

function getType($record)
{
    return $record['type'];
}