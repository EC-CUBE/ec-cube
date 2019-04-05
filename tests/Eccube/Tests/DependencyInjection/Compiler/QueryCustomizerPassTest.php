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

namespace Eccube\Tests\DependencyInjection\Compiler;

use Eccube\DependencyInjection\Compiler\QueryCustomizerPass;
use Eccube\Doctrine\Query\Queries;
use Eccube\Doctrine\Query\WhereCustomizer;
use Eccube\Repository\QueryKey;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class QueryCustomizerPassTest extends TestCase
{
    public function testAppendCustomizerToQueries()
    {
        $container = new ContainerBuilder();
        $container->register(Queries::class)
            ->setPublic(true);

        $container->register(TestQueryCustomizer::class)
            ->addTag(QueryCustomizerPass::QUERY_CUSTOMIZER_TAG);

        $container->addCompilerPass(new QueryCustomizerPass());
        $container->compile();

        // Queriesにカスタマイザが追加されていることを確認
        $queries = $container->get(Queries::class);
        $ref = new \ReflectionObject($queries);
        $prop = $ref->getProperty('customizers');
        $prop->setAccessible(true);
        $customizers = $prop->getValue($queries);

        self::assertCount(1, $customizers);
        self::assertArrayHasKey(QueryKey::CUSTOMER_SEARCH, $customizers);
        self::assertInstanceOf(TestQueryCustomizer::class, $customizers[QueryKey::CUSTOMER_SEARCH][0]);
    }
}

class TestQueryCustomizer extends WhereCustomizer
{
    protected function createStatements($params, $queryKey)
    {
    }

    public function getQueryKey()
    {
        return QueryKey::CUSTOMER_SEARCH;
    }
}
