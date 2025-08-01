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

use Doctrine\ORM\QueryBuilder;

/**
 * JOIN句を組み立てるクラス
 */
class JoinClause
{
    /**
     * @var JoinClauseWhereCustomizer
     */
    private $whereCustomizer;

    /**
     * @var JoinClauseOrderByCustomizer
     */
    private $orderByCustomizer;

    /**
     * JoinClause constructor.
     *
     * @param $join
     * @param $alias
     * @param $conditionType
     * @param $condition
     * @param $indexBy
     */
    private function __construct(private readonly bool $leftJoin, private $join, private $alias, private $conditionType = null, private $condition = null, private $indexBy = null)
    {
        $this->whereCustomizer = new JoinClauseWhereCustomizer();
        $this->orderByCustomizer = new JoinClauseOrderByCustomizer();
    }

    /**
     * INNER JOIN用のファクトリメソッド。
     *
     * @see QueryBuilder::innerJoin()
     *
     * @param $join
     * @param $alias
     * @param $conditionType
     * @param $condition
     * @param $indexBy
     *
     * @return JoinClause
     */
    public static function innerJoin($join, $alias, $conditionType = null, $condition = null, $indexBy = null): JoinClause
    {
        return new JoinClause(false, $join, $alias, $conditionType, $condition, $indexBy);
    }

    /**
     * LEFT JOIN用のファクトリメソッド。
     *
     * @see QueryBuilder::leftJoin()
     *
     * @param $join
     * @param $alias
     * @param $conditionType
     * @param $condition
     * @param $indexBy
     *
     * @return JoinClause
     */
    public static function leftJoin($join, $alias, $conditionType = null, $condition = null, $indexBy = null): JoinClause
    {
        return new JoinClause(true, $join, $alias, $conditionType, $condition, $indexBy);
    }

    /**
     * WHERE句を追加します。
     *
     *
     * @return $this
     */
    public function addWhere(WhereClause $whereClause): self
    {
        $this->whereCustomizer->add($whereClause);

        return $this;
    }

    /**
     * ORDER BY句を追加します。
     *
     *
     * @return $this
     */
    public function addOrderBy(OrderByClause $orderByClause): self
    {
        $this->orderByCustomizer->add($orderByClause);

        return $this;
    }

    public function build(QueryBuilder $builder)
    {
        if ($this->leftJoin) {
            $builder->leftJoin($this->join, $this->alias, $this->conditionType, $this->condition, $this->indexBy);
        } else {
            $builder->innerJoin($this->join, $this->alias, $this->conditionType, $this->condition, $this->indexBy);
        }
        $this->whereCustomizer->customize($builder, null, '');
        $this->orderByCustomizer->customize($builder, null, '');
    }
}

class JoinClauseWhereCustomizer extends WhereCustomizer
{
    /**
     * @var WhereClause[]
     */
    private $whereClauses = [];

    public function add(WhereClause $whereClause): void
    {
        $this->whereClauses[] = $whereClause;
    }

    /**
     * @param array $params
     * @param $queryKey
     *
     * @return WhereClause[]
     */
    protected function createStatements($params, $queryKey): array
    {
        return $this->whereClauses;
    }

    /**
     * カスタマイズ対象のキーを返します。
     *
     * @return string
     */
    public function getQueryKey(): string
    {
        return '';
    }
}

class JoinClauseOrderByCustomizer extends OrderByCustomizer
{
    /**
     * @var OrderByClause[]
     */
    private $orderByClauses = [];

    public function add(OrderByClause $orderByClause)
    {
        $this->orderByClauses[] = $orderByClause;
    }

    /**
     * @param array $params
     * @param $queryKey
     *
     * @return OrderByClause[]
     */
    protected function createStatements($params, $queryKey): array
    {
        return $this->orderByClauses;
    }

    /**
     * カスタマイズ対象のキーを返します。
     *
     * @return string
     */
    public function getQueryKey(): string
    {
        return '';
    }
}
