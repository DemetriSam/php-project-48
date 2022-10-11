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
    $this->expectedNested = file_get_contents('tests/fixtures/diff.stylish');

    $this->expectedJson = file_get_contents('tests/fixtures/diff.json');

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