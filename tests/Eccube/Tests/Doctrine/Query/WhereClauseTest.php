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

use Doctrine\ORM\Query\Parameter;
use Eccube\Doctrine\Query\WhereClause;
use Eccube\Tests\EccubeTestCase;

final class WhereClauseTest extends EccubeTestCase
{
    public function testEq()
    {
        $actual = WhereClause::eq('name', ':Name', 'foo');
        $this->assertSame('name = :Name', $this->asString($actual));
        $this->assertEquals([new Parameter('Name', 'foo')], $this->getParams($actual));
    }

    public function testEqWithMapParam()
    {
        $actual = WhereClause::eq('name', ':Name', ['Name' => 'foo']);
        $this->assertSame('name = :Name', $this->asString($actual));
        $this->assertEquals([new Parameter('Name', 'foo')], $this->getParams($actual));
    }

    public function testNeq()
    {
        $actual = WhereClause::neq('name', ':Name', 'foo');
        $this->assertSame('name <> :Name', $this->asString($actual));
        $this->assertEquals([new Parameter('Name', 'foo')], $this->getParams($actual));
    }

    public function testIsNull()
    {
        $actual = WhereClause::isNull('name');
        $this->assertSame('name IS NULL', $this->asString($actual));
        $this->assertSame([], $this->getParams($actual));
    }

    public function testIsNotNull()
    {
        $actual = WhereClause::isNotNull('name');
        $this->assertSame('name IS NOT NULL', $this->asString($actual));
        $this->assertSame([], $this->getParams($actual));
    }

    public function testLike()
    {
        $actual = WhereClause::like('name', ':Name', '%hoge');
        $this->assertSame('name LIKE :Name', $this->asString($actual));
        $this->assertEquals([new Parameter('Name', '%hoge')], $this->getParams($actual));
    }

    public function testNotLike()
    {
        $actual = WhereClause::notLike('name', ':Name', '%hoge');
        $this->assertSame('name NOT LIKE :Name', $this->asString($actual));
        $this->assertEquals([new Parameter('Name', '%hoge')], $this->getParams($actual));
    }

    public function testIn()
    {
        $actual = WhereClause::in('name', ':Names', ['foo', 'bar']);
        $this->assertSame('name IN(:Names)', $this->asString($actual));
        $this->assertEquals([new Parameter('Names', ['foo', 'bar'])], $this->getParams($actual));
    }

    public function testInWithMap()
    {
        $actual = WhereClause::in('name', ':Names', ['Names' => ['foo', 'bar']]);
        $this->assertSame('name IN(:Names)', $this->asString($actual));
        $this->assertEquals([new Parameter('Names', ['foo', 'bar'])], $this->getParams($actual));
    }

    public function testNotIn()
    {
        $actual = WhereClause::notIn('name', ':Names', ['foo', 'bar']);
        $this->assertSame('name NOT IN(:Names)', $this->asString($actual));
        $this->assertEquals([new Parameter('Names', ['foo', 'bar'])], $this->getParams($actual));
    }

    public function testNotInWithMap()
    {
        $actual = WhereClause::notIn('name', ':Names', ['Names' => ['foo', 'bar']]);
        $this->assertSame('name NOT IN(:Names)', $this->asString($actual));
        $this->assertEquals([new Parameter('Names', ['foo', 'bar'])], $this->getParams($actual));
    }

    public function testBetween()
    {
        $actual = WhereClause::between('price', ':Min', ':Max', [1000, 2000]);
        $this->assertSame('price BETWEEN :Min AND :Max', $this->asString($actual));
        $this->assertEquals([new Parameter('Min', 1000), new Parameter('Max', 2000)], $this->getParams($actual));
    }

    public function testBetweenWithMap()
    {
        $actual = WhereClause::between('price', ':Min', ':Max', ['Min' => 1000, 'Max' => 2000]);
        $this->assertSame('price BETWEEN :Min AND :Max', $this->asString($actual));
        $this->assertEquals([new Parameter('Min', 1000), new Parameter('Max', 2000)], $this->getParams($actual));
    }

    public function testGt()
    {
        $actual = WhereClause::gt('price', ':Price', 1000);
        $this->assertSame('price > :Price', $this->asString($actual));
        $this->assertEquals([new Parameter('Price', 1000)], $this->getParams($actual));
    }

    public function testGtWithMap()
    {
        $actual = WhereClause::gt('price', ':Price', ['Price' => 1000]);
        $this->assertSame('price > :Price', $this->asString($actual));
        $this->assertEquals([new Parameter('Price', 1000)], $this->getParams($actual));
    }

    public function testGte()
    {
        $actual = WhereClause::gte('price', ':Price', 1000);
        $this->assertSame('price >= :Price', $this->asString($actual));
        $this->assertEquals([new Parameter('Price', 1000)], $this->getParams($actual));
    }

    public function testGteWithMap()
    {
        $actual = WhereClause::gte('price', ':Price', ['Price' => 1000]);
        $this->assertSame('price >= :Price', $this->asString($actual));
        $this->assertEquals([new Parameter('Price', 1000)], $this->getParams($actual));
    }

    public function testLt()
    {
        $actual = WhereClause::lt('price', ':Price', 1000);
        $this->assertSame('price < :Price', $this->asString($actual));
        $this->assertEquals([new Parameter('Price', 1000)], $this->getParams($actual));
    }

    public function testLtWithMap()
    {
        $actual = WhereClause::lt('price', ':Price', ['Price' => 1000]);
        $this->assertSame('price < :Price', $this->asString($actual));
        $this->assertEquals([new Parameter('Price', 1000)], $this->getParams($actual));
    }

    public function testLte()
    {
        $actual = WhereClause::lte('price', ':Price', 1000);
        $this->assertSame('price <= :Price', $this->asString($actual));
        $this->assertEquals([new Parameter('Price', 1000)], $this->getParams($actual));
    }

    public function testLteWithMap()
    {
        $actual = WhereClause::lte('price', ':Price', ['Price' => 1000]);
        $this->assertSame('price <= :Price', $this->asString($actual));
        $this->assertEquals([new Parameter('Price', 1000)], $this->getParams($actual));
    }

    public function testParameterNull()
    {
        $actual = WhereClause::eq('name', ':Name', null);
        $this->assertSame('name = :Name', $this->asString($actual));
        $this->assertEquals([new Parameter('Name', null)], $this->getParams($actual));
    }

    public function testParameterFalseEquivalent()
    {
        $actual = WhereClause::eq('name', ':Name', '0');
        $this->assertSame('name = :Name', $this->asString($actual));
        $this->assertEquals([new Parameter('Name', '0')], $this->getParams($actual));
    }

    /*
     * Helper methods.
     */

    private function asString(WhereClause $clause)
    {
        $builder = $this->entityManager->createQueryBuilder();
        $clause->build($builder);

        return preg_replace('/^SELECT WHERE /', '', $builder->getDQL());
    }

    private function getParams(WhereClause $clause)
    {
        $builder = $this->entityManager->createQueryBuilder();
        $clause->build($builder);

        return $builder->getParameters()->toArray();
    }
}
