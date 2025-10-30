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
     * @var string
     */
    private string $join;

    /**
     * @var string
     */
    private string $alias;

    /**
     * @var string|null
     */
    private ?string $conditionType;
    /**
     * @var string|null
     */
    private ?string $condition;
    /**
     * @var string|null
     */
    private ?string $indexBy;
    /**
     * @var bool
     */
    private bool $leftJoin;

    /**
     * @var JoinClauseWhereCustomizer
     */
    private JoinClauseWhereCustomizer $whereCustomizer;

    /**
     * @var JoinClauseOrderByCustomizer
     */
    private JoinClauseOrderByCustomizer $orderByCustomizer;

    /**
     * JoinClause constructor.
     */
    private function __construct(bool $leftJoin, string $join, string $alias, ?string $conditionType = null, ?string $condition = null, ?string $indexBy = null)
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
     */
    public static function innerJoin(string $join, string $alias, ?string $conditionType = null, ?string $condition = null, ?string $indexBy = null): JoinClause
    {
        return new JoinClause(false, $join, $alias, $conditionType, $condition, $indexBy);
    }

    /**
     * LEFT JOIN用のファクトリメソッド。
     *
     * @see QueryBuilder::leftJoin()
     */
    public static function leftJoin(string $join, string $alias, ?string $conditionType = null, ?string $condition = null, ?string $indexBy = null): JoinClause
    {
        return new JoinClause(true, $join, $alias, $conditionType, $condition, $indexBy);
    }

    /**
     * WHERE句を追加します。
     *
     * @return $this
     */
    public function addWhere(WhereClause $whereClause): static
    {
        $this->whereCustomizer->add($whereClause);

        return $this;
    }

    /**
     * ORDER BY句を追加します。
     *
     * @return $this
     */
    public function addOrderBy(OrderByClause $orderByClause): static
    {
        $this->orderByCustomizer->add($orderByClause);

        return $this;
    }

    public function build(QueryBuilder $builder): void
    {
        if ($this->leftJoin) {
            $builder->leftJoin($this->join, $this->alias, $this->conditionType, $this->condition, $this->indexBy);
        } else {
            $builder->innerJoin($this->join, $this->alias, $this->conditionType, $this->condition, $this->indexBy);
        }
        $this->whereCustomizer->customize($builder, [], '');
        $this->orderByCustomizer->customize($builder, [], '');
    }
}

class JoinClauseWhereCustomizer extends WhereCustomizer
{
    /**
     * @var WhereClause[]
     */
    private array $whereClauses = [];

    public function add(WhereClause $whereClause): void
    {
        $this->whereClauses[] = $whereClause;
    }

    /**
     * @param array<mixed> $params
     * @param string $queryKey
     *
     * @return WhereClause[]
     */
    #[\Override]
    protected function createStatements($params, $queryKey): array
    {
        return $this->whereClauses;
    }

    /**
     * カスタマイズ対象のキーを返します。
     */
    #[\Override]
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
    private array $orderByClauses = [];

    public function add(OrderByClause $orderByClause): void
    {
        $this->orderByClauses[] = $orderByClause;
    }

    /**
     * @param array<mixed> $params
     * @param string $queryKey
     *
     * @return OrderByClause[]
     */
    #[\Override]
    protected function createStatements($params, $queryKey): array
    {
        return $this->orderByClauses;
    }

    /**
     * カスタマイズ対象のキーを返します。
     */
    #[\Override]
    public function getQueryKey(): string
    {
        return '';
    }
}
