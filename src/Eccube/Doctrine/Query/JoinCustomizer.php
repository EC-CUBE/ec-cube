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
 * JOIN句をカスタマイズするクラス。
 *
 * @package Eccube\Doctrine\Query
 */
abstract class JoinCustomizer implements QueryCustomizer
{

    /**
     * @param QueryBuilder $builder
     * @param array $params
     * @param string $queryKey
     */
    public final function customize(QueryBuilder $builder, $params, $queryKey)
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
     * @return JoinClause[]
     */
    public abstract function createStatements($params, $queryKey);

}