<?php

namespace Php\Package\Tests;

use PHPUnit\Framework\TestCase;
use function Gen\Diff\genDiff;
use function Gen\Diff\parseFile;

class GenDiffTest extends TestCase
{

    private $expected;

    public function setUp(): void
    {
        $this->expected = [
            'follow' => [
                'old' => false,
                'actual' => NULL,
                'diff' => 'deleted',
                'type' => 'leaf',
            ],
            'host' => [
                'old' => 'hexlet.io',
                'actual' => 'hexlet.io',
                'diff' =>   'same',
                'type' => 'leaf',
            ],
            'proxy' => [
                'old' => '123.234.53.22',
                'actual' => NULL,
                'diff' => 'deleted',
                'type' => 'leaf',
            ],
            'timeout' => [
                'old' => 50,
                'actual' => 20,
                'diff' => 'changed',
                'type' => 'leaf',
            ],
            'verbose' => [
                'old' => NULL,
                'actual' => true,
                'diff' => 'added',
                'type' => 'leaf',
            ],
        ];
    }

    /**
     * @covers Gen\Diff\genDiff
     * @covers Gen\Diff\parseFile
     * @covers Gen\Diff\parseJson
     * @covers Gen\Diff\parseYaml
     */
    public function testGenDiffJson()
    {
        $first = parseFile('tests/fixtures/json/file1.json');
        $second = parseFile('tests/fixtures/json/file2.json');

        $actual = genDiff($first, $second);

        $this->assertEquals($this->expected, $actual);
    }

    /**
     * @covers Gen\Diff\genDiff
     * @covers Gen\Diff\parseFile
     * @covers Gen\Diff\parseJson
     * @covers Gen\Diff\parseYaml
     */
    public function testGenDiffYaml()
    {
        $first = parseFile('tests/fixtures/yaml/file1.yaml');
        $second = parseFile('tests/fixtures/yaml/file2.yaml');

        $actual = genDiff($first, $second);

        $this->assertEquals($this->expected, $actual);
    }

    public function testGenDiffRec()
    {
        $first = parseFile('tests/fixtures/rec/file1.json');
        $second = parseFile('tests/fixtures/rec/file2.json');

        $actual = genDiff($first, $second);

        //print_r($actual);
    }
    
}