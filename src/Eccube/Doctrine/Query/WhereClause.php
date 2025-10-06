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
use Doctrine\ORM\QueryBuilder;

/**
 * WHERE句を組み立てるクラス。
 */
class WhereClause
{
    /** @var Expr\Comparison|string */
    private $expr;

    /**
     * @var string|array<string, int|string>|null
     */
    private $params;

    /**
     * WhereClause constructor.
     *
     * @param Expr\Comparison|string $expr
     * @param string|array<string, int|string>|null $params
     */
    private function __construct($expr, $params = null)
    {
        $this->expr = $expr;
        $this->params = $params;
    }

    /**
     * @param Expr\Comparison $expr
     * @param string $x
     * @param string|array<string, int|string> $y
     *
     * @return self
     */
    private static function newWhereClause($expr, $x, $y): WhereClause
    {
        if (is_array($y)) {
            return new WhereClause($expr, $y);
        } else {
            return new WhereClause($expr, [$x => $y]);
        }
    }

    /**
     * =条件式のファクトリメソッド。
     *
     * Example:
     *      WhereClause::eq('name', ':Name', 'hoge')
     *      WhereClause::eq('name', ':Name', ['Name' => 'hoge'])
     *
     * @param string $x
     * @param string $y
     * @param string|array<string, int|string>|null $param
     *
     * @return WhereClause
     */
    public static function eq($x, $y, $param): WhereClause
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
     * @param string $x
     * @param string $y
     * @param string|array<string, int|string>|null $param
     *
     * @return WhereClause
     */
    public static function neq($x, $y, $param): WhereClause
    {
        return self::newWhereClause(self::expr()->neq($x, $y), $y, $param);
    }

    /**
     * IS NULL条件式のファクトリメソッド。
     *
     * Example:
     *      WhereClause::isNull('name')
     *
     * @param string $x
     *
     * @return WhereClause
     */
    public static function isNull($x): WhereClause
    {
        return new WhereClause(self::expr()->isNull($x));
    }

    /**
     * IS NOT NULL条件式のファクトリメソッド。
     *
     * Example:
     *      WhereClause::isNotNull('name')
     *
     * @param string $x
     *
     * @return WhereClause
     */
    public static function isNotNull($x): WhereClause
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
     * @param string $x
     * @param string $y
     * @param string|array<string, int|string>|null $param
     *
     * @return WhereClause
     */
    public static function like($x, $y, $param): WhereClause
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
     * @param string $x
     * @param string $y
     * @param string|array<string, int|string>|null $param
     *
     * @return WhereClause
     */
    public static function notLike($x, $y, $param): WhereClause
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
     * @param string $x
     * @param string $y
     * @param string|array<string, int|string>|null $param
     *
     * @return WhereClause
     */
    public static function in($x, $y, $param): WhereClause
    {
        return new WhereClause(self::expr()->in($x, $y), self::isMap($param) ? $param : [$y => $param]);
    }

    /**
     * @param array<int, string>|array<string, string> $arrayOrMap
     *
     * @return bool
     */
    private static function isMap($arrayOrMap): bool
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
     * @param string $x
     * @param string $y
     * @param string|array<string, int|string>|null $param
     *
     * @return WhereClause
     */
    public static function notIn($x, $y, $param): WhereClause
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
     * @param string $var
     * @param string $x
     * @param string $y
     * @param string|array<string, int|string>|null $params
     *
     * @return WhereClause
     */
    public static function between($var, $x, $y, $params): WhereClause
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
     * @param string $x
     * @param string $y
     * @param string|array<string, int|string>|null $param
     *
     * @return WhereClause
     */
    public static function gt($x, $y, $param): WhereClause
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
     * @param string $x
     * @param string $y
     * @param string|array<string, int|string>|null $param
     *
     * @return WhereClause
     */
    public static function gte($x, $y, $param): WhereClause
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
     * @param string $x
     * @param string $y
     * @param string|array<string, int|string>|null $param
     *
     * @return WhereClause
     */
    public static function lt($x, $y, $param): WhereClause
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
     * @param string $x
     * @param string $y
     * @param string|array<string, int|string>|null $param
     *
     * @return WhereClause
     */
    public static function lte($x, $y, $param): WhereClause
    {
        return self::newWhereClause(self::expr()->lte($x, $y), $y, $param);
    }

    /**
     * @return Expr
     */
    private static function expr(): Expr
    {
        return new Expr();
    }

    /**
     * QueryBuilderにWHERE句を組み立てます。
     *
     * @param QueryBuilder $builder
     *
     * @return void
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
