<?php

namespace Eccube\Tests\Doctrine\Query;

use Doctrine\ORM\QueryBuilder;
use Eccube\Doctrine\Query\WhereClause;
use Eccube\Doctrine\Query\WhereCustomizer;
use Eccube\Tests\EccubeTestCase;

class WhereCustomizerTest extends EccubeTestCase
{

    public function testCustomizeNOP()
    {
        $builder = $this->createQueryBuilder();
        $customizer = new WhereCustomizerTest_Customizer(function() { return []; });
        $customizer->customize($builder, null, '');

        self::assertEquals('SELECT p FROM Product p', $builder->getDQL());
    }

    public function testCustomizeAddWhereClause()
    {
        $builder = $this->createQueryBuilder();
        $customizer = new WhereCustomizerTest_Customizer(function() { return [WhereClause::eq('name', ':Name', 'hoge')]; });
        $customizer->customize($builder, null, '');

        self::assertEquals('SELECT p FROM Product p WHERE name = :Name', $builder->getDQL());
    }

    public function testCustomizeAddMultipleWhereClause()
    {
        $builder = $this->createQueryBuilder();
        $customizer = new WhereCustomizerTest_Customizer(function() { return [
            WhereClause::eq('name', ':Name', 'hoge'),
            WhereClause::eq('delFlg', ':DelFlg', 0)
        ]; });
        $customizer->customize($builder, null, '');

        self::assertEquals('SELECT p FROM Product p WHERE name = :Name AND delFlg = :DelFlg', $builder->getDQL());
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

class WhereCustomizerTest_Customizer extends WhereCustomizer
{
    /**
     * @var callable $callback
     */
    private $callback;

    function __construct($callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param array $params
     * @param $queryKey
     * @return WhereClause[]
     */
    protected function createStatements($params, $queryKey)
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