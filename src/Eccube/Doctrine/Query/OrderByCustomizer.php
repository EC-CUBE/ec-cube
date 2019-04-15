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
 * ORDER BY句をカスタマイズするクラス。
 */
abstract class OrderByCustomizer implements QueryCustomizer
{
    /**
     * @param QueryBuilder $builder
     * @param array $params
     * @param string $queryKey
     */
    final public function customize(QueryBuilder $builder, $params, $queryKey)
    {
        foreach ($this->createStatements($params, $queryKey) as $index => $orderByClause) {
            if ($index === 0) {
                $builder->orderBy($orderByClause->getSort(), $orderByClause->getOrder());
            } else {
                $builder->addOrderBy($orderByClause->getSort(), $orderByClause->getOrder());
            }
        }
    }

    /**
     * 変更するORDER BY句を組み立てます。
     * このメソッドの戻り値で、元のクエリのORDER BY句が上書きされます。
     *
     * @param array $params
     * @param $queryKey
     *
     * @return OrderByClause[]
     */
    abstract protected function createStatements($params, $queryKey);
}
