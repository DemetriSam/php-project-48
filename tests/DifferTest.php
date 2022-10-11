<?php

// https://ru.hexlet.io/blog/posts/how-to-test-code
// https://ru.hexlet.io/courses/js-advanced-testing/lessons/fixtures/theory_unit

// Наружу выставляется тот же неймспейс что и у основного кода
namespace Hexlet\Project\tests;

use PHPUnit\Framework\TestCase;

// Импорт ровно одной функции, той что является интерфейсной функцией библиотеки
use function Differ\Differ\genDiff;

// Может быть методом, но не обязательно
function getFixtureFullPath($fixtureName)
{
    $parts = [__DIR__, 'fixtures', $fixtureName];
    return realpath(implode(DIRECTORY_SEPARATOR, $parts));
}

// Можно сократить дублирование если использовать data provider
// Данные (фикстуры в текстовых файлах)
// Тесты плоских структур полностью покрываются тестами на вложенные структуры
class DifferTest extends TestCase
{
    /**
     * @dataProvider formatProvider
     */
    public function testDefault($format)
    {
        $filepath1 = getFixtureFullPath("file1.{$format}");
        $filepath2 = getFixtureFullPath("file2.{$format}");

        $pathToResult = getFixtureFullPath('diff.stylish');
        $this->assertStringEqualsFile($pathToResult, genDiff($filepath1, $filepath2));
    }

    /**
     * @dataProvider formatProvider
     */
    public function testStylish($format)
    {
        $filepath1 = getFixtureFullPath("file1.{$format}");
        $filepath2 = getFixtureFullPath("file2.{$format}");

        $pathToResult = getFixtureFullPath('diff.stylish');
        $this->assertStringEqualsFile($pathToResult, genDiff($filepath1, $filepath2, 'stylish'));
    }

    /**
     * @dataProvider formatProvider
     */
    public function testPlain($format)
    {
        $filepath1 = getFixtureFullPath("file1.{$format}");
        $filepath2 = getFixtureFullPath("file2.{$format}");

        $pathToResult = getFixtureFullPath('diff.plain');
        $this->assertStringEqualsFile($pathToResult, genDiff($filepath1, $filepath2, 'plain'));
    }

    /**
     * @dataProvider formatProvider
     */
    public function testJson($format)
    {
        $filepath1 = getFixtureFullPath("file1.{$format}");
        $filepath2 = getFixtureFullPath("file2.{$format}");

        // $pathToResult = getFixtureFullPath('diff.json');
        // $this->assertStringEqualsFile($pathToResult, genDiff($filepath1, $filepath2, 'json'));
        $data = genDiff($filepath1, $filepath2, 'json');
        try {
            json_decode($data, null, 512, JSON_THROW_ON_ERROR);
        } catch (\Exception $e) {
            print($data);
            throw $e;
        }
        $this->assertTrue(true);
    }

    public function formatProvider()
    {
        return [
            'json files' => ['json'],
            'yaml files' => ['yaml'],
        ];
    }
}
