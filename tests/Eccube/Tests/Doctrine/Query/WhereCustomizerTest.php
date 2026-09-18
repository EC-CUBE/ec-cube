<?php

declare(strict_types=1);

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
use Eccube\Doctrine\Query\WhereClause;
use Eccube\Doctrine\Query\WhereCustomizer;
use Eccube\Tests\EccubeTestCase;

final class WhereCustomizerTest extends EccubeTestCase
{
    public function testCustomizeNOP()
    {
        $builder = $this->createQueryBuilder();
        $customizer = new WhereCustomizerTest_Customizer(fn () => []);
        $customizer->customize($builder, null, '');

        $this->assertSame('SELECT p FROM Product p', $builder->getDQL());
    }

    public function testCustomizeAddWhereClause()
    {
        $builder = $this->createQueryBuilder();
        $customizer = new WhereCustomizerTest_Customizer(fn () => [WhereClause::eq('name', ':Name', 'hoge')]);
        $customizer->customize($builder, null, '');

        $this->assertSame('SELECT p FROM Product p WHERE name = :Name', $builder->getDQL());
    }

    public function testCustomizeAddMultipleWhereClause()
    {
        $builder = $this->createQueryBuilder();
        $customizer = new WhereCustomizerTest_Customizer(fn () => [
            WhereClause::eq('name', ':Name', 'hoge'),
            WhereClause::eq('delFlg', ':DelFlg', 0),
        ]);
        $customizer->customize($builder, null, '');

        $this->assertSame('SELECT p FROM Product p WHERE name = :Name AND delFlg = :DelFlg', $builder->getDQL());
    }

    private function createQueryBuilder(): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->select('p')
            ->from('Product', 'p');
    }
}

class WhereCustomizerTest_Customizer extends WhereCustomizer
{
    /**
     * @var callable
     */
    private $callback;

    public function __construct($callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param $queryKey
     *
     * @return WhereClause[]
     */
    protected function createStatements(array $params, $queryKey): array
    {
        $callback = $this->callback;

        return $callback($params);
    }

    /**
     * カスタマイズ対象のキーを返します。
     */
    public function getQueryKey(): string
    {
        return '';
    }
}
