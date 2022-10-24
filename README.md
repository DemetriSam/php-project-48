### Hexlet tests and linter status:
[![Actions Status](https://github.com/DemetriSam/php-project-48/workflows/hexlet-check/badge.svg)](https://github.com/DemetriSam/php-project-48/actions)
[![Actions Status](https://github.com/DemetriSam/php-project-48/workflows/my-check/badge.svg)](https://github.com/DemetriSam/php-project-48/actions)
<a href="https://codeclimate.com/github/DemetriSam/php-project-48/maintainability"><img src="https://api.codeclimate.com/v1/badges/0709e5b47749fe0666be/maintainability" /></a>
<a href="https://codeclimate.com/github/DemetriSam/php-project-48/test_coverage"><img src="https://api.codeclimate.com/v1/badges/0709e5b47749fe0666be/test_coverage" /></a>


# Вычислитель отличий

Второй проект на платформе Хекслет. 
Вычислитель отличий - это cli-утилита сравнивающая два объекта, их свойства и значения. Поддерживаются объекты в формате yaml и json. Вывод возможен в формате json, в формате дерева, и плоского списка всех изменений.

## Демонстрация
https://asciinema.org/a/531597

## Требования

- PHP >= 8.0
- Composer >= 2
- make >= 4

## Установка
- make install

## Использование

 - в качестве cli утилиты: команда gendiff (справка: "gendiff -h")
 - в качестве библиотеки в вашем проекте: подключить Differ\Differ\genDiff()
