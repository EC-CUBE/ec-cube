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

namespace Eccube\Tests\Doctrine\Query;

use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\QueryBuilder;
use Eccube\Doctrine\Query\JoinClause;
use Eccube\Doctrine\Query\OrderByClause;
use Eccube\Doctrine\Query\WhereClause;
use Eccube\Tests\EccubeTestCase;

class JoinClauseTest extends EccubeTestCase
{
    public function testInnerJoin()
    {
        $clause = JoinClause::innerJoin('p.ProductCategories', 'pct');
        self::assertEquals('INNER JOIN p.ProductCategories pct', $this->asString($clause));
    }

    public function testLeftJoin()
    {
        $clause = JoinClause::leftJoin('p.ProductCategories', 'pct');
        self::assertEquals('LEFT JOIN p.ProductCategories pct', $this->asString($clause));
    }

    public function testInnerJoinFull()
    {
        $clause = JoinClause::innerJoin('p.ProductCategories', 'pct', 'ON', 'pct.sort_no = 1', 'categoryId');
        self::assertEquals('INNER JOIN p.ProductCategories pct INDEX BY categoryId ON pct.sort_no = 1', $this->asString($clause));
    }

    public function testLeftJoinFull()
    {
        $clause = JoinClause::leftJoin('p.ProductCategories', 'pct', 'ON', 'pct.sort_no = 1', 'categoryId');
        self::assertEquals('LEFT JOIN p.ProductCategories pct INDEX BY categoryId ON pct.sort_no = 1', $this->asString($clause));
    }

    public function testWithWhere()
    {
        $clause = JoinClause::leftJoin('p.ProductCategories', 'pct')
            ->addWhere(WhereClause::eq('p.name', ':Name', 'hoge'))
            ->addWhere(WhereClause::eq('pct.sort_no', ':SortNo', 1));
        self::assertEquals('LEFT JOIN p.ProductCategories pct WHERE p.name = :Name AND pct.sort_no = :SortNo', $this->asString($clause));
        self::assertEquals([new Parameter('Name', 'hoge'), new Parameter('SortNo', 1)], $this->getParams($clause));
    }

    public function testWithOrderBy()
    {
        $clause = JoinClause::leftJoin('p.ProductCategories', 'pct')
            ->addOrderBy(new OrderByClause('pct.sort_no', 'desc'))
            ->addOrderBy(new OrderByClause('pct.categoryId'));
        self::assertEquals('LEFT JOIN p.ProductCategories pct ORDER BY pct.sort_no desc, pct.categoryId asc', $this->asString($clause));
    }

    private function asString(JoinClause $clause)
    {
        $builder = $this->queryBuilder();
        $clause->build($builder);

        return preg_replace('/^SELECT p FROM Product p /', '', $builder->getDQL());
    }

    private function getParams(JoinClause $clause)
    {
        $builder = $this->queryBuilder();
        $clause->build($builder);

        return $builder->getParameters()->toArray();
    }

    /**
     * @return QueryBuilder
     */
    private function queryBuilder()
    {
        return $this->entityManager->createQueryBuilder()
            ->select('p')->from('Product', 'p');
    }
}
