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
        $this->expectedNested = <<<E
{
    common: {
      + follow: false
        setting1: Value 1
      - setting2: 200
      - setting3: true
      + setting3: null
      + setting4: blah blah
      + setting5: {
            key5: value5
        }
        setting6: {
            doge: {
              - wow:
              + wow: so much
            }
            key: value
          + ops: vops
        }
    }
    group1: {
      - baz: bas
      + baz: bars
        foo: bar
      - nest: {
            key: value
        }
      + nest: str
    }
  - group2: {
        abc: 12345
        deep: {
            id: 45
        }
    }
  + group3: {
        deep: {
            id: {
                number: 45
            }
        }
        fee: 100500
    }
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

    public function testMakeStylishStringRecursive()
    {
        $first = parseFile('tests/fixtures/rec/file1.json');
        $second = parseFile('tests/fixtures/rec/file2.json');

        $diff = genDiff($first, $second);
        $actual = makeStylishString($diff);

        //$this->assertEquals($this->expectedNested, $actual);
    }
}