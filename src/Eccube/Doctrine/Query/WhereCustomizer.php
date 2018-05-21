<?php

namespace Eccube\Doctrine\Query;

use Doctrine\ORM\QueryBuilder;

abstract class WhereCustomizer implements QueryCustomizer
{
    /**
     * @param QueryBuilder $builder
     * @param array $params
     * @param string $queryKey
     */
    final public function customize(QueryBuilder $builder, $params, $queryKey)
    {
        foreach ($this->createStatements($params, $queryKey) as $whereClause) {
            $whereClause->build($builder);
        }
    }

    /**
     * @param array $params
     * @param $queryKey
     *
     * @return WhereClause[]
     */
    abstract protected function createStatements($params, $queryKey);
}
