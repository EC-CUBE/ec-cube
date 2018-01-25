<?php

namespace Eccube\Tests\DependencyInjection\Compiler;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use Eccube\Annotation\QueryExtension;
use Eccube\DependencyInjection\Compiler\AutoConfigurationTagPass;
use Eccube\DependencyInjection\Compiler\QueryCustomizerPass;
use Eccube\Doctrine\Query\Queries;
use Eccube\Doctrine\Query\QueryCustomizer;
use Eccube\Doctrine\Query\WhereCustomizer;
use Eccube\Repository\QueryKey;
use Eccube\Util\ReflectionUtil;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class QueryCustomizerPassTest extends TestCase
{
    public function testAppendCustomizerToQueries()
    {
        $container = new ContainerBuilder();
        $container->register(Reader::class, AnnotationReader::class);
        $container->register(Queries::class)
            ->addArgument(new Reference(Reader::class))
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


/**
 * @QueryExtension(QueryKey::CUSTOMER_SEARCH)
 */
class TestQueryCustomizer extends WhereCustomizer
{
    protected function createStatements($params, $queryKey)
    {
    }
}
