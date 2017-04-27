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
 * ORDER BY句をカスタマイズするクラス。
 *
 * @package Eccube\Doctrine\Query
 */
abstract class OrderByCustomizer implements QueryCustomizer
{

    /**
     * @param QueryBuilder $builder
     * @param array $params
     * @param string $queryKey
     */
    public final function customize(QueryBuilder $builder, $params, $queryKey)
    {
        foreach ($this->createStatements($params, $queryKey) as $index=>$orderByClause) {
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
     * @return OrderByClause[]
     */
    protected abstract function createStatements($params, $queryKey);
}