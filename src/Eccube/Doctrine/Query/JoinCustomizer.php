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
     * @param array<mixed>|null $params
     *
     * @return void
     */
    #[\Override]
    final public function customize(QueryBuilder $builder, ?array $params, string $queryKey): void
    {
        $params ??= [];
        foreach ($this->createStatements($params, $queryKey) as $joinClause) {
            $joinClause->build($builder);
        }
    }

    /**
     * 追加するJOIN句を組み立てます。
     * このメソッドの戻り値が、元のクエリのJOIN句に追加されます。
     *
     * @param array<mixed> $params
     *
     * @return JoinClause[]
     */
    abstract public function createStatements(array $params, string $queryKey): array;
}
