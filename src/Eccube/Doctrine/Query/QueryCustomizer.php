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
 * クエリをカスタマイズするインターフェイス。
 */
interface QueryCustomizer
{
    /**
     * クエリをカスタマイズします。
     *
     * @param QueryBuilder $builder
     * @param array $params
     * @param string $queryKey
     */
    public function customize(QueryBuilder $builder, $params, $queryKey);

    /**
     * カスタマイズ対象のキーを返します。
     *
     * @return string
     */
    public function getQueryKey();
}
