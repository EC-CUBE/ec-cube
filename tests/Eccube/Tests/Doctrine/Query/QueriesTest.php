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

use Doctrine\ORM\QueryBuilder;
use Eccube\Doctrine\Query\Queries;
use Eccube\Doctrine\Query\QueryCustomizer;
use Eccube\Tests\EccubeTestCase;

class QueriesTest extends EccubeTestCase
{
    public function testCustomizerShouldBeCalled()
    {
        $customizer = new QueriesTest_Customizer();
        $queries = new Queries();
        $queries->addCustomizer($customizer);

        $queries->customize(QueriesTest::class, $this->queryBuilder(), null);

        self::assertTrue($customizer->customized);
    }

    public function testCustomizerShouldNotBeCalled()
    {
        $customizer = new QueriesTest_Customizer();
        $queries = new Queries();
        $queries->addCustomizer($customizer);

        $queries->customize('Dummy', $this->queryBuilder(), null);

        self::assertFalse($customizer->customized);
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

class QueriesTest_Customizer implements QueryCustomizer
{
    public $customized = false;

    public function customize(QueryBuilder $builder, $params, $queryKey)
    {
        $this->customized = true;
    }

    /**
     * カスタマイズ対象のキーを返します。
     *
     * @return string
     */
    public function getQueryKey()
    {
        return QueriesTest::class;
    }
}

class QueriesTest_CustomizerWithoutAnnotation implements QueryCustomizer
{
    public function customize(QueryBuilder $builder, $params, $queryKey)
    {
    }

    public function getQueryKey()
    {
        return;
    }
}
