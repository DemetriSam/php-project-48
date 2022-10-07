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

    $this->expectedJson = file_get_contents('tests/fixtures/result.json');

    $this->expectedFromPlainFormatter = <<<E
Property 'common.follow' was added with value: false
Property 'common.setting2' was removed
Property 'common.setting3' was updated. From true to null
Property 'common.setting4' was added with value: 'blah blah'
Property 'common.setting5' was added with value: [complex value]
Property 'common.setting6.doge.wow' was updated. From '' to 'so much'
Property 'common.setting6.ops' was added with value: 'vops'
Property 'group1.baz' was updated. From 'bas' to 'bars'
Property 'group1.nest' was updated. From [complex value] to 'str'
Property 'group2' was removed
Property 'group3' was added with value: [complex value]
E;
  }

  /**
   * @group plain
   */
  public function testMakeStylishString()
  {
    $first = parseFile('tests/fixtures/json/file1.json');
    $second = parseFile('tests/fixtures/json/file2.json');

    $actual = genDiff($first, $second);

    $this->assertEquals($this->expectedPlain, $actual);
  }

  /**
   * @group recursive
   */
  public function testMakeStylishStringRecursive()
  {
    $first = parseFile('tests/fixtures/rec/file1.json');
    $second = parseFile('tests/fixtures/rec/file2.json');

    $actual = genDiff($first, $second);  

    $this->assertEquals($this->expectedNested, $actual);
  }

  public function testPlainFormatter()
  {
    $first = parseFile('tests/fixtures/rec/file1.json');
    $second = parseFile('tests/fixtures/rec/file2.json');

    $actual = genDiff($first, $second, 'plain');  

    $this->assertEquals($this->expectedFromPlainFormatter, $actual);
  }

  public function testJsonFormatter()
  {
    $first = parseFile('tests/fixtures/rec/file1.json');
    $second = parseFile('tests/fixtures/rec/file2.json');

    $actual = genDiff($first, $second, 'json');  

    $this->assertEquals($this->expectedJson, $actual);
  }

}