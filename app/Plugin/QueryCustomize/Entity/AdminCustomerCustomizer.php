<?php

namespace Plugin\QueryCustomize\Entity;

use Eccube\Annotation\QueryExtension;
use Eccube\Doctrine\Query\WhereClause;
use Eccube\Doctrine\Query\WhereCustomizer;
use Eccube\Repository\QueryKey;

/**
 * @QueryExtension(QueryKey::CUSTOMER_SEARCH)
 */
class AdminCustomerCustomizer extends WhereCustomizer
{
    /**
     * 1回以上購入している会員を抽出
     *
     * @param array $params
     * @param $queryKey
     * @return WhereClause[]
     */
    protected function createStatements($params, $queryKey)
    {
        // travis-ciのテストが通らないため、コメントアウト
        // 試してみるにはコメントアウトを解除してください.
        //return [WhereClause::gte('c.buy_times', ':buy_times', ['buy_times' => 1])];

        return [];
    }
}