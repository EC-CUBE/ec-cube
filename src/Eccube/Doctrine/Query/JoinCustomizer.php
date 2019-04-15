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
 * JOIN句をカスタマイズするクラス。
 */
abstract class JoinCustomizer implements QueryCustomizer
{
    /**
     * @param QueryBuilder $builder
     * @param array $params
     * @param string $queryKey
     */
    final public function customize(QueryBuilder $builder, $params, $queryKey)
    {
        foreach ($this->createStatements($params, $queryKey) as $joinClause) {
            $joinClause->build($builder);
        }
    }

    /**
     * 追加するJOIN句を組み立てます。
     * このメソッドの戻り値が、元のクエリのJOIN句に追加されます。
     *
     * @param array $params
     * @param $queryKey
     *
     * @return JoinClause[]
     */
    abstract public function createStatements($params, $queryKey);
}
