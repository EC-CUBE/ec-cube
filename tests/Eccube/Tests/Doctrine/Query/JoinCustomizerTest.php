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
use Eccube\Doctrine\Query\JoinClause;
use Eccube\Doctrine\Query\JoinCustomizer;
use Eccube\Tests\EccubeTestCase;

class JoinCustomizerTest extends EccubeTestCase
{
    public function testCustomize()
    {
        $builder = $this->createQueryBuilder();
        $customizer = new JoinCustomizerTest_Customizer(function () { return []; });
        $customizer->customize($builder, null, '');
        self::assertEquals($builder->getDQL(), 'SELECT p FROM Product p');
    }

    public function testCustomizeInnerJoin()
    {
        $builder = $this->createQueryBuilder();
        $customizer = new JoinCustomizerTest_Customizer(function () {
            return [
            JoinClause::innerJoin('p.ProductCategories', 'pct'),
        ];
        });
        $customizer->customize($builder, null, '');
        self::assertEquals($builder->getDQL(), 'SELECT p FROM Product p INNER JOIN p.ProductCategories pct');
    }

    public function testCustomizeMultiInnerJoin()
    {
        $builder = $this->createQueryBuilder();
        $customizer = new JoinCustomizerTest_Customizer(function () {
            return [
            JoinClause::innerJoin('p.ProductCategories', 'pct'),
            JoinClause::innerJoin('pct.Category', 'c'),
        ];
        });
        $customizer->customize($builder, null, '');
        self::assertEquals($builder->getDQL(), 'SELECT p FROM Product p INNER JOIN p.ProductCategories pct INNER JOIN pct.Category c');
    }

    /**
     * @return QueryBuilder
     */
    private function createQueryBuilder()
    {
        return $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from('Product', 'p');
    }
}

class JoinCustomizerTest_Customizer extends JoinCustomizer
{
    private $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param array $params
     * @param $queryKey
     *
     * @return JoinClause[]
     */
    public function createStatements($params, $queryKey)
    {
        $callback = $this->callback;

        return $callback($params);
    }

    /**
     * カスタマイズ対象のキーを返します。
     *
     * @return string
     */
    public function getQueryKey()
    {
        return '';
    }
}
