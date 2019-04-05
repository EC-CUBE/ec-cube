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

class Queries
{
    /**
     * @var QueryCustomizer[]
     */
    private $customizers = [];

    public function addCustomizer(QueryCustomizer $customizer)
    {
        $queryKey = $customizer->getQueryKey();
        $this->customizers[$queryKey][] = $customizer;
    }

    public function customize($queryKey, QueryBuilder $builder, $params)
    {
        if (isset($this->customizers[$queryKey])) {
            /* @var QueryCustomizer $customizer */
            foreach ($this->customizers[$queryKey] as $customizer) {
                $customizer->customize($builder, $params, $queryKey);
            }
        }

        return $builder;
    }
}
