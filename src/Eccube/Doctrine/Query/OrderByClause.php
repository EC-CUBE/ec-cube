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
     * @var string
     */
    private string $sort;

    /**
     * @var string
     */
    private string $order;

    /**
     * OrderByClause constructor.
     */
    public function __construct(string $sort, string $order = 'asc')
    {
        $this->sort = $sort;
        $this->order = $order;
    }

    public function getSort(): string
    {
        return $this->sort;
    }

    public function getOrder(): string
    {
        return $this->order;
    }
}
