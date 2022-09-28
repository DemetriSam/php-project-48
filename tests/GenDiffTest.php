<?php

namespace Php\Package\Tests;

use PHPUnit\Framework\TestCase;
use function Gen\Diff\genDiff;

class GenDiffTest extends TestCase
{
    public function testGenDiff()
    {

        $first = (array) json_decode(file_get_contents('tests/fixtures/example1/file1.json'));
        $second = (array) json_decode(file_get_contents('tests/fixtures/example1/file2.json'));

        $expected = [
            'follow' => [
                'old' => false,
                'actual' => NULL,
                'diff' => 'deleted',
            ],
            'host' => [
                'old' => 'hexlet.io',
                'actual' => 'hexlet.io',
                'diff' =>   'same',
            ],
            'proxy' => [
                'old' => '123.234.53.22',
                'actual' => NULL,
                'diff' => 'deleted',
            ],
            'timeout' => [
                'old' => 50,
                'actual' => 20,
                'diff' => 'changed',
            ],
            'verbose' => [
                'old' => NULL,
                'actual' => true,
                'diff' => 'added',
            ],
        ];

        $actual = genDiff($first, $second);

        $this->assertEquals($expected, $actual);
    }
}