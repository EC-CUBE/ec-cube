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

/**
 * ORDER BY句を組み立てるクラス。
 */
class OrderByClause
{
    /**
     * OrderByClause constructor.
     *
     * @param $sort
     * @param string $order
     */
    public function __construct(private $sort, private $order = 'asc')
    {
    }

    /**
     * @return string
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @return string
     */
    public function getOrder()
    {
        return $this->order;
    }
}
