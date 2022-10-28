<?php

namespace Php\Package\Tests;

use PHPUnit\Framework\TestCase;

use function Differ\Differ\genDiff;

class GenDiffTest extends TestCase
{
    /**
     * @dataProvider provideFixtures
     */
    public function testStylishFormatter($first, $second, $format, $expected)
    {
        $actual = genDiff($first, $second, $format);
        $this->assertEquals($expected, $actual);
    }

    public function provideFixtures()
    {

        $json1 = 'tests/fixtures/file1.json';
        $json2 = 'tests/fixtures/file2.json';

        $yaml1 = 'tests/fixtures/file1.yaml';
        $yaml2 = 'tests/fixtures/file2.yaml';

        $expectedStylish = file_get_contents('tests/fixtures/diff.stylish');
        $expectedJson = file_get_contents('tests/fixtures/diff.json');
        $expectedPlain = file_get_contents('tests/fixtures/diff.plain');

        return [
            [$json1, $json2, 'stylish', $expectedStylish],
            [$yaml1, $yaml2, 'stylish', $expectedStylish],
            [$json1, $yaml2, 'stylish', $expectedStylish],
            [$json1, $json2, 'plain', $expectedPlain],
            [$yaml1, $yaml2, 'plain', $expectedPlain],
            [$json1, $yaml2, 'plain', $expectedPlain],
            [$json1, $json2, 'json', $expectedJson],
            [$yaml1, $yaml2, 'json', $expectedJson],
            [$json1, $yaml2, 'json', $expectedJson],
        ];
    }
}
