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

namespace Eccube\Service\Product;


use DateTime;
use Eccube\Doctrine\Query\WhereClause;
use Eccube\Entity\Product;

class PublishTermVisibility extends ProductVisibility
{
    public function checkVisibility(Product $Product)
    {
        $now = new DateTime();

        if ($Product->getPublishStart() && $Product->getPublishEnd()) {
            return $Product->getPublishStart() <= $now && $Product->getPublishEnd() > $now;
        } elseif ($Product->getPublishStart()) {
            return $Product->getPublishStart() <= $now;
        } elseif ($Product->getPublishEnd()) {
            return $Product->getPublishEnd() > $now;
        }

        return true;
    }

    protected function createStatements($params, $queryKey)
    {
        $now = new DateTime();

        $start = WhereClause::lt('p.publishStart', ':publishStart', $now);
        $end = WhereClause::gte('p.publishEnd', ':publishEnd', $now);
        $startIsNull = WhereClause::isNull('p.publishEnd');
        $endIsNull = WhereClause::isNull('p.publishStart');

        return [WhereClause::or(
            WhereClause::and($start, $end),
            WhereClause::and($start, $startIsNull),
            WhereClause::and($endIsNull, $end),
            WhereClause::and($startIsNull, $endIsNull)
        )];
    }
}
