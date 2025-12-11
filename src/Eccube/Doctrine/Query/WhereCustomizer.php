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

abstract class WhereCustomizer implements QueryCustomizer
{
    /**
     * @param array<string, mixed>|null $params
     */
    #[\Override]
    final public function customize(QueryBuilder $builder, ?array $params, string $queryKey): void
    {
        $params ??= [];
        foreach ($this->createStatements($params, $queryKey) as $whereClause) {
            $whereClause->build($builder);
        }
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return WhereClause[]
     */
    abstract protected function createStatements(array $params, string $queryKey): array;
}
