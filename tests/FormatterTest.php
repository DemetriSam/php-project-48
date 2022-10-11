<?php

namespace Php\Package\Tests;

use PHPUnit\Framework\TestCase;
use function Differ\Differ\genDiff;
use function Differ\Differ\parseFile;
use function Differ\Differ\makeStylishString;
use function Differ\Differ\stringify;

class FormatterTest extends TestCase
{
  private $expectedPlain;

  public function setUp(): void
  {
  $this->expectedNested = <<<E
{
    common: {
      + follow: false
        setting1: Value 1
      - setting2: 200
      - setting3: true
      + setting3: {
            key: value
        }
      + setting4: blah blah
      + setting5: {
            key5: value5
        }
        setting6: {
            doge: {
              - wow: too much
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
    group4: {
      - default: null
      + default:
      - foo: 0
      + foo: null
      - isNested: false
      + isNested: none
      + key: false
        nest: {
          - bar:
          + bar: 0
          - isNested: true
        }
      + someKey: true
      - type: bas
      + type: bar
    }
}
E;

    $this->expectedJson = json_encode(json_decode(file_get_contents('tests/fixtures/diff.json'), true), JSON_PRETTY_PRINT);

    $this->expectedFromPlainFormatter = file_get_contents('tests/fixtures/diff.plain');
  }

  public function testMakeStylishStringRecursive()
  {
    $first = 'tests/fixtures/file1.json';
    $second = 'tests/fixtures/file2.json';

    $actual = genDiff($first, $second);  

    $this->assertEquals($this->expectedNested, $actual);
  }

  public function testPlainFormatter()
  {
    $first = 'tests/fixtures/file1.json';
    $second = 'tests/fixtures/file2.json';

    $actual = genDiff($first, $second, 'plain');  

    $this->assertEquals($this->expectedFromPlainFormatter, $actual);
  }

  public function testJsonFormatter()
  {
    $first = 'tests/fixtures/file1.json';
    $second = 'tests/fixtures/file2.json';

    $actual = genDiff($first, $second, 'json');  

    $this->assertEquals($this->expectedJson, $actual);
  }

}