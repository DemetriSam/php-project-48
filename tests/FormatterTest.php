<?php

namespace Php\Package\Tests;

use PHPUnit\Framework\TestCase;
use function Gen\Diff\genDiff;
use function Gen\Diff\parseFile;
use function Gen\Diff\makeStylishString;

class FormatterTest extends TestCase
{
    private $expectedPlain;

    public function setUp(): void
    {
        $this->expectedPlain = <<<E
{
 - follow: false
   host: hexlet.io
 - proxy: 123.234.53.22
 - timeout: 50
 + timeout: 20
 + verbose: true
}

E;
    }

    /**
     * @covers Gen\Diff\makeStylishString
     * @covers Gen\Diff\genDiff
     * @covers Gen\Diff\parseFile
     * @covers Gen\Diff\parseJson
     * @covers Gen\Diff\parseYaml
     */
    public function testMakeStylishString()
    {
        $first = parseFile('tests/fixtures/json/file1.json');
        $second = parseFile('tests/fixtures/json/file2.json');

        $diff = genDiff($first, $second);
        $actual = makeStylishString($diff);

        $this->assertEquals($this->expectedPlain, $actual);
    }
}