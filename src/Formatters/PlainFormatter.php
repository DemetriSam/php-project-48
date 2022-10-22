<?php

namespace Differ\Differ\PlainFormatter;

use Differ\Differ;

function render(array $tree)
{
    $basket = [];
    $lines = reduce(putStringInBasket(), $tree, $basket);
    return implode("\n", $lines);
}

/**
 * Функция reduce аналог array_reduce, только для дерева
 */
function reduce(callable $putStringInBasket, array $tree, array $init, array $path = [])
{
    $type = Differ\getType($tree);
    $key = Differ\getKey($tree);

    switch ($type) {
        case 'root':
        case 'nested':
            $recursiveAcc = $putStringInBasket($init, $tree, $path);
            $children = Differ\getChildren($tree);

            return array_reduce(
                $children,
                fn($acc, $child) => reduce(
                    $putStringInBasket,
                    $child,
                    $acc,
                    array_filter(
                        array_merge($path, [$key]),
                        fn($item) => $item !== null
                    )
                ),
                $recursiveAcc
            );

        case 'changed':
        case 'deleted':
        case 'added':
        case 'unchanged':
            return $putStringInBasket($init, $tree, $path);
        default:
            throw new \Exception("Unknown or not existed state");
    }
}

/**
 * Возвращает callback для reduce
 */
function putStringInBasket()
{
    return function ($init, $node, $path) {

        $type = Differ\getType($node);
        $key = Differ\getKey($node);
        $currentPath = implode('.', array_merge($path, [$key]));

        switch ($type) {//кладем в корзинку к reduce строку, сформированную на основе данных и состояния ноды
            case 'changed':
                [$value1, $value2] = Differ\getValue($node);

                $renderedValue1 = stringify($value1);
                $renderedValue2 = stringify($value2);

                return array_merge(
                    $init,
                    ["Property '{$currentPath}' was updated. From {$renderedValue1} to {$renderedValue2}"]
                );

            case 'deleted':
                return array_merge(
                    $init,
                    ["Property '{$currentPath}' was removed"]
                );

            case 'added':
                $value = Differ\getValue($node);
                $renderedValue = stringify($value);

                return array_merge(
                    $init,
                    ["Property '{$currentPath}' was added with value: {$renderedValue}"]
                );

            case 'root':
            case 'nested':
            case 'unchanged':
                return $init;
                //для тех состояний, которые не должны попадать в вывод, не кладем в корзинку ничего

            default:
                throw new \Exception("Unknown or not existed state");
        }
    };
}

function stringify($value)
{
    return is_array($value) ? '[complex value]' : Differ\toString($value, false);
}
