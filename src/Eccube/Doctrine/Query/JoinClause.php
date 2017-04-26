<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Doctrine\Query;


use Doctrine\ORM\QueryBuilder;

/**
 * JOIN句を組み立てるクラス
 */
class JoinClause
{

    private $join;

    private $alias;

    private $conditionType;

    private $condition;

    private $indexBy;

    private $leftJoin = false;

    /**
     * @var JoinClauseWhereCustomizer $whereCustomizer
     */
    private $whereCustomizer;

    /**
     * @var JoinClauseOrderByCustomizer $orderByCustomizer
     */
    private $orderByCustomizer;

    /**
     * JoinClause constructor.
     * @param $leftJoin
     * @param $join
     * @param $alias
     * @param $conditionType
     * @param $condition
     * @param $indexBy
     */
    private function __construct($leftJoin, $join, $alias, $conditionType = null, $condition = null, $indexBy = null)
    {
        $this->leftJoin = $leftJoin;
        $this->join = $join;
        $this->alias = $alias;
        $this->conditionType = $conditionType;
        $this->condition = $condition;
        $this->indexBy = $indexBy;
        $this->whereCustomizer = new JoinClauseWhereCustomizer();
        $this->orderByCustomizer = new JoinClauseOrderByCustomizer();
    }

    /**
     * INNER JOIN用のファクトリメソッド。
     *
     * @see QueryBuilder::innerJoin()
     * @param $join
     * @param $alias
     * @param $conditionType
     * @param $condition
     * @param $indexBy
     * @return JoinClause
     */
    public static function innerJoin($join, $alias, $conditionType = null, $condition = null, $indexBy = null)
    {
        return new JoinClause(false, $join, $alias, $conditionType, $condition, $indexBy);
    }

    /**
     * LEFT JOIN用のファクトリメソッド。
     *
     * @see QueryBuilder::leftJoin()
     * @param $join
     * @param $alias
     * @param $conditionType
     * @param $condition
     * @param $indexBy
     * @return JoinClause
     */
    public static function leftJoin($join, $alias, $conditionType = null, $condition = null, $indexBy = null)
    {
        return new JoinClause(true, $join, $alias, $conditionType, $condition, $indexBy);
    }

    /**
     * WHERE句を追加します。
     *
     * @param WhereClause $whereClause
     * @return $this
     */
    public function addWhere(WhereClause $whereClause)
    {
        $this->whereCustomizer->add($whereClause);
        return $this;
    }

    /**
     * ORDER BY句を追加します。
     * @param OrderByClause $orderByClause
     * @return $this
     */
    public function addOrderBy(OrderByClause $orderByClause)
    {
        $this->orderByCustomizer->add($orderByClause);
        return $this;
    }

    public function build(QueryBuilder $builder) {
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

    public function add(WhereClause $whereClause)
    {
        $this->whereClauses[] = $whereClause;
    }

    /**
     * @param array $params
     * @param $queryKey
     * @return WhereClause[]
     */
    protected function createStatements($params, $queryKey)
    {
        return $this->whereClauses;
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
     * @return OrderByClause[]
     */
    protected function createStatements($params, $queryKey)
    {
        return $this->orderByClauses;
    }
}