<?php

namespace Eccube\Tests\Doctrine\Query;

use Doctrine\ORM\QueryBuilder;
use Eccube\Annotation\QueryExtension;
use Eccube\Doctrine\Query\Queries;
use Eccube\Doctrine\Query\QueryCustomizer;
use Eccube\Tests\EccubeTestCase;
use Psr\Log\InvalidArgumentException;

class QueriesTest extends EccubeTestCase
{

    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
    }

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

    public function testAddCustomizerWithoutAnnotation()
    {
        $customizer = new QueriesTest_CustomizerWithoutAnnotation();
        $queries = new Queries();

        $this->setExpectedException(InvalidArgumentException::class);

        $queries->addCustomizer($customizer);
    }

    /**
     * @return QueryBuilder
     */
    private function queryBuilder()
    {
        return $this->app['orm.em']->createQueryBuilder()
            ->select('p')->from('Product', 'p');
    }

}

/**
 * @QueryExtension(QueriesTest::class)
 */
class QueriesTest_Customizer implements QueryCustomizer
{

    public $customized = false;

    public function customize(QueryBuilder $builder, $params, $queryKey)
    {
        $this->customized = true;
    }
}

class QueriesTest_CustomizerWithoutAnnotation implements QueryCustomizer
{
    public function customize(QueryBuilder $builder, $params, $queryKey) {}
}