<?php


namespace Acme\Entity;


use Eccube\Annotation\QueryExtension;
use Eccube\Doctrine\Query\OrderByClause;
use Eccube\Doctrine\Query\OrderByCustomizer;
use Eccube\Repository\QueryKey;

/**
 * @QueryExtension(QueryKey::PRODUCT_SEARCH_ADMIN)
 */
class AdminProductListCustomizer extends OrderByCustomizer
{
    /**
     * 常に商品IDでソートする。
     *
     * @param array $params
     * @param $queryKey
     * @return OrderByClause[]
     */
    protected function createStatements($params, $queryKey)
    {
        return [new OrderByClause('p.id')];
    }
}