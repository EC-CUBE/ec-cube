<?php


namespace Acme\Doctrine\Query;


use Eccube\Doctrine\Query\OrderByClause;
use Eccube\Doctrine\Query\OrderByCustomizer;
use Eccube\Repository\QueryKey;

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
        return [];
        // このサンプルを動作させるには、以下を有効にしてください.
        // return [new OrderByClause('p.id')];
    }

    /**
     * カスタマイズ対象のキーを返します。
     *
     * @return string
     */
    public function getQueryKey()
    {
        return QueryKey::PRODUCT_SEARCH_ADMIN;
    }
}