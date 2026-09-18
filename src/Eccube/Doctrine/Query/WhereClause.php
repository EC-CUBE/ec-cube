<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Doctrine\Query;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;

/**
 * WHERE句を組み立てるクラス。
 */
class WhereClause
{
    /**
     * WhereClause constructor.
     *
     * @param string|int|array<string, int|string|null>|null $params
     */
    private function __construct(private readonly Comparison|string $expr, private readonly string|int|array|null $params = null)
    {
    }

    /**
     * @param string|int|array<string, int|string>|null $y
     */
    private static function newWhereClause(Comparison $expr, string $x, string|int|array|null $y): WhereClause
    {
        if (is_array($y)) {
            return new WhereClause($expr, $y);
        } elseif ($y === null) {
            return new WhereClause($expr, [$x => null]);
        }

        return new WhereClause($expr, [$x => $y]);
    }

    /**
     * =条件式のファクトリメソッド。
     *
     * Example:
     *      WhereClause::eq('name', ':Name', 'hoge')
     *      WhereClause::eq('name', ':Name', ['Name' => 'hoge'])
     *
     * @param string|int|array<string, int|string>|null $param
     */
    public static function eq(string $x, string $y, string|int|array|null $param): WhereClause
    {
        return self::newWhereClause(self::expr()->eq($x, $y), $y, $param);
    }

    /**
     * <>条件式のファクトリメソッド。
     *
     * Example:
     *      WhereClause::neq('name', ':Name', 'hoge')
     *      WhereClause::neq('name', ':Name', ['Name' => 'hoge'])
     *
     * @param string|int|array<string, int|string>|null $param
     */
    public static function neq(string $x, string $y, string|int|array|null $param): WhereClause
    {
        return self::newWhereClause(self::expr()->neq($x, $y), $y, $param);
    }

    /**
     * IS NULL条件式のファクトリメソッド。
     *
     * Example:
     *      WhereClause::isNull('name')
     */
    public static function isNull(string $x): WhereClause
    {
        return new WhereClause(self::expr()->isNull($x));
    }

    /**
     * IS NOT NULL条件式のファクトリメソッド。
     *
     * Example:
     *      WhereClause::isNotNull('name')
     */
    public static function isNotNull(string $x): WhereClause
    {
        return new WhereClause(self::expr()->isNotNull($x));
    }

    /**
     * LIKE演算子のファクトリメソッド。
     *
     * Example:
     *      WhereClause::like('name', ':Name', '%hoge')
     *      WhereClause::like('name', ':Name', ['Name' => '%hoge'])
     *
     * @param string|int|array<string, int|string>|null $param
     */
    public static function like(string $x, string $y, string|int|array|null $param): WhereClause
    {
        return self::newWhereClause(self::expr()->like($x, $y), $y, $param);
    }

    /**
     * NOT LIKE演算子のファクトリメソッド。
     *
     * Example:
     *      WhereClause::notLike('name', ':Name', '%hoge')
     *      WhereClause::notLike('name', ':Name', ['Name' => '%hoge'])
     *
     * @param string|int|array<string, int|string>|null $param
     */
    public static function notLike(string $x, string $y, string|int|array|null $param): WhereClause
    {
        return self::newWhereClause(self::expr()->notLike($x, $y), $y, $param);
    }

    /**
     * IN演算子のファクトリメソッド。
     *
     * Example:
     *      WhereClause::in('name', ':Names', ['foo', 'bar'])
     *      WhereClause::in('name', ':Names', ['Names' => ['foo', 'bar']])
     *
     * @param string|int|array<string, int|string>|null $param
     */
    public static function in(string $x, string $y, string|int|array|null $param): WhereClause
    {
        return new WhereClause(self::expr()->in($x, $y), self::isMap($param) ? $param : [$y => $param]);
    }

    /**
     * @param array<int, string>|array<string, string> $arrayOrMap
     */
    private static function isMap(array $arrayOrMap): bool
    {
        return array_values($arrayOrMap) !== $arrayOrMap;
    }

    /**
     * NOT IN演算子のファクトリメソッド。
     *
     * Example:
     *      WhereClause::notIn('name', ':Names', ['foo', 'bar'])
     *      WhereClause::notIn('name', ':Names', ['Names' => ['foo', 'bar']])
     *
     * @param string|int|array<string, int|string>|null $param
     */
    public static function notIn(string $x, string $y, string|int|array|null $param): WhereClause
    {
        return new WhereClause(self::expr()->notIn($x, $y), self::isMap($param) ? $param : [$y => $param]);
    }

    /**
     * BETWEEN演算子のファクトリメソッド。
     *
     * Example:
     *      WhereClause::between('price', ':PriceMin', ':PriceMax', [1000, 2000])
     *      WhereClause::between('price', ':PriceMin', ':PriceMax', ['PriceMin' => 1000, 'PriceMax' => 2000])
     *
     * @param string|int|array<string, int|string>|null $params
     */
    public static function between(string $var, string $x, string $y, string|int|array|null $params): WhereClause
    {
        return new WhereClause(self::expr()->between($var, $x, $y), self::isMap($params) ? $params : [$x => $params[0], $y => $params[1]]);
    }

    /**
     * >演算子のファクトリメソッド。
     *
     * Example:
     *      WhereClause::gt('price', ':Price', 1000)
     *      WhereClause::gt('price', ':Price', ['Price' => 1000])
     *
     * @param string|int|array<string, int|string>|null $param
     */
    public static function gt(string $x, string $y, string|int|array|null $param): WhereClause
    {
        return self::newWhereClause(self::expr()->gt($x, $y), $y, $param);
    }

    /**
     * >=演算子のファクトリメソッド。
     *
     * Example:
     *      WhereClause::gte('price', ':Price', 1000)
     *      WhereClause::gte('price', ':Price', ['Price' => 1000])
     *
     * @param string|int|array<string, int|string>|null $param
     */
    public static function gte(string $x, string $y, string|int|array|null $param): WhereClause
    {
        return self::newWhereClause(self::expr()->gte($x, $y), $y, $param);
    }

    /**
     * <演算子のファクトリメソッド。
     *
     * Example:
     *      WhereClause::lt('price', ':Price', 1000)
     *      WhereClause::lt('price', ':Price', ['Price' => 1000])
     *
     * @param string|int|array<string, int|string>|null $param
     */
    public static function lt(string $x, string $y, string|int|array|null $param): WhereClause
    {
        return self::newWhereClause(self::expr()->lt($x, $y), $y, $param);
    }

    /**
     * <=演算子のファクトリメソッド。
     *
     * Example:
     *      WhereClause::lte('price', ':Price', 1000)
     *      WhereClause::lte('price', ':Price', ['Price' => 1000])
     *
     * @param string|int|array<string, int|string>|null $param
     */
    public static function lte(string $x, string $y, string|int|array|null $param): WhereClause
    {
        return self::newWhereClause(self::expr()->lte($x, $y), $y, $param);
    }

    private static function expr(): Expr
    {
        return new Expr();
    }

    /**
     * QueryBuilderにWHERE句を組み立てます。
     */
    public function build(QueryBuilder $builder): void
    {
        $builder->andWhere($this->expr);
        if ($this->params) {
            foreach ($this->params as $key => $param) {
                $builder->setParameter($key, $param);
            }
        }
    }
}
