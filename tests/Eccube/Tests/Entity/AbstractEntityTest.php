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

namespace Eccube\Tests\Entity;

use Doctrine\ORM\Mapping\Id;
use Eccube\Entity\AbstractEntity;
use Eccube\Tests\EccubeTestCase;

/**
 * AbstractEntity test cases.
 *
 * @author Kentaro Ohkouchi
 */
class AbstractEntityTest extends EccubeTestCase
{
    private $objEntity;

    public function testNewInstance()
    {
        $arrProps = [
            'field1' => 1,
            'field2' => 2,
            'field3' => 3,
            'testField4' => 4,
        ];
        $this->objEntity = new TestEntity($arrProps);
        $this->assertTrue(is_object($this->objEntity));
    }

    public function testNewInstanceEmptyParams()
    {
        $this->objEntity = new TestEntity();
        $this->assertTrue(is_object($this->objEntity));
    }

    public function testToArray()
    {
        $arrProps = [
            'field1' => 1,
            'field2' => 2,
            'field3' => 3,
            'testField4' => 4,
        ];
        $this->objEntity = new TestEntity($arrProps);
        $expected = $arrProps;
        $actual = $this->objEntity->toArray();
        $this->assertEquals($expected, $actual);
    }

    public function testSetPropertiesFromArray()
    {
        $arrProps = [
            'field2' => null,
            'field3' => 3,
            'field4' => 4,
            'testField4' => 5,
            'fieldXXX' => 'XXX',
        ];

        $this->objEntity = new TestEntity();
        $this->objEntity->setField1('a');
        $this->objEntity->setField2('b');
        $this->objEntity->field3 = 'c';

        $this->objEntity->setPropertiesFromArray($arrProps);

        $this->assertEquals($this->objEntity->getField1(), 'a');
        $this->assertNull($this->objEntity->getField2(), 'field2 is null');
        $this->assertEquals($this->objEntity->field3, 3);
        $this->assertEquals($this->objEntity->getTestField4(), 5);
    }

    public function testGetter()
    {
        $arrProps = [
            'field1' => 1,
            'field2' => 2,
            'field3' => 3,
            'testField4' => 4,
        ];
        $this->objEntity = new TestEntity($arrProps);
        $this->assertEquals($this->objEntity->getField1(), 1);
        $this->assertEquals($this->objEntity->getField2(), 2);
        $this->assertEquals($this->objEntity->field3, 3);
        $this->assertEquals($this->objEntity->getTestField4(), 4);
    }

    public function testExtends()
    {
        $arrProps = [
            'field1' => 1,
            'field2' => 2,
            'field3' => 3,
            'field4' => 4,
            'testField4' => 5,
        ];
        $this->objEntity = new TestExtendsEntity($arrProps);
        $this->assertEquals($this->objEntity->getField1(), 1);
        $this->assertEquals($this->objEntity->getField2(), 2);
        $this->assertEquals($this->objEntity->field3, 3);
        $this->assertEquals($this->objEntity->getField4(), 4);
        $this->assertEquals($this->objEntity->getTestField4(), 5);
        $expected = $arrProps;
        $actual = $this->objEntity->toArray();
        $this->assertEquals($expected, $actual);
    }

    public function testChildrens()
    {
        $Date = new \DateTime('2017-09-25 00:00:00 +00:00');
        $TestChildrens = new \Doctrine\Common\Collections\ArrayCollection();
        $TestChildrens[] = new TestChildren('child1');
        $TestChildrens[] = new TestChildren('child2');
        $TestChildrens[] = new TestChildren('child3');
        $arrProps = [
            'field1' => 1,
            'field2' => 2,
            'field3' => 3,
            'field4' => $Date,
            'testField4' => 5,
            'TestChildrens' => $TestChildrens,
        ];

        $this->objEntity = new TestChildEntity($arrProps);
        $this->assertEquals($this->objEntity->getField1(), 1);
        $this->assertEquals($this->objEntity->getField2(), 2);
        $this->assertEquals($this->objEntity->field3, 3);
        $this->assertEquals($this->objEntity->getField4(), $Date);
        $this->assertEquals($this->objEntity->getTestField4(), 5);
        $expected = $arrProps;
        $actual = $this->objEntity->toArray();

        $this->assertEquals($expected, $actual);
    }

    public function testChildrensWithToNormalizedArray()
    {
        $Date = new \DateTime('2017-09-25 00:00:00 +00:00');
        $TestChildrens = new \Doctrine\Common\Collections\ArrayCollection();
        $TestChildrens[] = new TestChildren('child1');
        $TestChildrens[] = new TestChildren('child2');
        $TestChildrens[] = new TestChildren('child3');
        $arrProps = [
            'field1' => 1,
            'field2' => 2,
            'field3' => 3,
            'field4' => $Date,
            'testField4' => 5,
            'TestChildrens' => $TestChildrens,
        ];

        $this->objEntity = new TestChildEntity($arrProps);
        $this->assertEquals($this->objEntity->getField1(), 1);
        $this->assertEquals($this->objEntity->getField2(), 2);
        $this->assertEquals($this->objEntity->field3, 3);
        $this->assertEquals($this->objEntity->getField4(), $Date);
        $this->assertEquals($this->objEntity->getTestField4(), 5);
        $expected = $arrProps;
        $expected['field4'] = '2017-09-25T00:00:00Z';
        $expected['TestChildrens'] = [
            ['childField' => 'child1'],
            ['childField' => 'child2'],
            ['childField' => 'child3'],
        ];
        $actual = $this->objEntity->toNormalizedArray();
        $this->assertEquals($expected, $actual);
    }

    public function testChildrensWithToJSON()
    {
        $Date = new \DateTime('2017-09-25 00:00:00 +00:00');
        $TestChildrens = new \Doctrine\Common\Collections\ArrayCollection();
        $TestChildrens[] = new TestChildren('child1');
        $TestChildrens[] = new TestChildren('child2');
        $TestChildrens[] = new TestChildren('child3');
        $arrProps = [
            'field1' => 1,
            'field2' => 2,
            'field3' => 3,
            'field4' => $Date,
            'testField4' => 5,
            'TestChildrens' => $TestChildrens,
        ];

        $this->objEntity = new TestChildEntity($arrProps);
        $this->assertEquals($this->objEntity->getField1(), 1);
        $this->assertEquals($this->objEntity->getField2(), 2);
        $this->assertEquals($this->objEntity->field3, 3);
        $this->assertEquals($this->objEntity->getField4(), $Date);
        $this->assertEquals($this->objEntity->getTestField4(), 5);
        $expected = $arrProps;
        $expected['field4'] = '2017-09-25T00:00:00Z';
        $expected['TestChildrens'] = [
            ['childField' => 'child1'],
            ['childField' => 'child2'],
            ['childField' => 'child3'],
        ];
        $actual = $this->objEntity->toJSON();

        $this->assertEquals($expected, json_decode($actual, true));
    }

    public function testChildrensWithToXML()
    {
        $Date = new \DateTime('2017-09-25 00:00:00 +00:00');
        $TestChildrens = new \Doctrine\Common\Collections\ArrayCollection();
        $TestChildrens[] = new TestChildren('child1');
        $TestChildrens[] = new TestChildren('child2');
        $TestChildrens[] = new TestChildren('child3');
        $arrProps = [
            'field1' => 1,
            'field2' => 2,
            'field3' => 3,
            'field4' => $Date,
            'testField4' => 5,
            'TestChildrens' => $TestChildrens,
        ];

        $this->objEntity = new TestChildEntity($arrProps);
        $this->assertEquals($this->objEntity->getField1(), 1);
        $this->assertEquals($this->objEntity->getField2(), 2);
        $this->assertEquals($this->objEntity->field3, 3);
        $this->assertEquals($this->objEntity->getField4(), $Date);
        $this->assertEquals($this->objEntity->getTestField4(), 5);

        $expected = '<?xml version="1.0"?>'.PHP_EOL;
        $expected .= '<TestChildEntity><field1>1</field1><field2>2</field2><field3>3</field3><testField4>5</testField4><field4>2017-09-25T00:00:00Z</field4><TestChildrens><childField>child1</childField></TestChildrens><TestChildrens><childField>child2</childField></TestChildrens><TestChildrens><childField>child3</childField></TestChildrens></TestChildEntity>'.PHP_EOL;
        $actual = $this->objEntity->toXML();

        $this->assertEquals($expected, $actual);
    }

    public function testCopyProperties()
    {
        $arrProps = [
            'field1' => 1,
            'field2' => 2,
            'field3' => 3,
            'field4' => 4,
            'testField4' => 5,
        ];
        $srcEntity = new TestExtendsEntity($arrProps);
        $destEntity = new TestExtendsEntity();

        // srcEntity から destEntity へフィールドをコピーする
        $destEntity->copyProperties($srcEntity);
        $this->assertEquals($destEntity->getField1(), 1);
        $this->assertEquals($destEntity->getField2(), 2);
        $this->assertEquals($destEntity->field3, 3);
        $this->assertEquals($destEntity->getField4(), 4);
        $this->assertEquals($destEntity->getTestField4(), 5);

        $expected = $arrProps;
        $actual = $destEntity->toArray();
        $this->assertEquals($expected, $actual);
    }

    public function testExcludeAttribute()
    {
        $arrProps = [
            'field1' => 1,
            'field2' => 2,
            'field3' => 3,
            'field4' => 4,
            'testField4' => 5,
        ];
        $srcEntity = new TestExtendsEntity($arrProps);
        $destEntity = new TestExtendsEntity();

        $destEntity->copyProperties($srcEntity, ['field1']); // field1 は除外
        $this->assertNull($destEntity->getField1());
        $this->assertEquals($destEntity->getField2(), 2);
        $this->assertEquals($destEntity->field3, 3);
        $this->assertEquals($destEntity->getField4(), 4);
        $this->assertEquals($destEntity->getTestField4(), 5);

        $expected = $arrProps;
        $expected['field1'] = null;
        $actual = $destEntity->toArray();
        $this->assertEquals($expected, $actual);
    }

    public function testCopyPropertiesWithNull()
    {
        $destEntity = new TestExtendsEntity();
        $destEntity->setField2(2);

        // field2 は NULL で上書きする
        $arrProps = [
            'field1' => 1,
            'field2' => null,
            'field3' => 3,
            'field4' => 4,
            'testField4' => 5,
            'fieldXXX' => 'XXX',
        ];
        $srcEntity = new TestExtendsEntity($arrProps);

        $destEntity->copyProperties($srcEntity);
        $this->assertEquals($destEntity->getField1(), 1);
        $this->assertNull($destEntity->getField2(), 'field2 is null');
        $this->assertEquals($destEntity->field3, 3);
        $this->assertEquals($destEntity->getField4(), 4);
        $this->assertEquals($destEntity->getTestField4(), 5);
    }
}

class TestEntity extends AbstractEntity
{
    private $field1;
    private $field2;
    /** public field */
    public $field3;
    /** camel case */
    private $testField4;

    public function __construct($arrProps = [])
    {
        if (is_array($arrProps) && count($arrProps) > 0) {
            $this->setPropertiesFromArray($arrProps);
        }
    }

    public function getField1()
    {
        return $this->field1;
    }

    public function setField1($field1)
    {
        $this->field1 = $field1;

        return $this;
    }

    public function getField2()
    {
        return $this->field2;
    }

    public function setField2($field2)
    {
        $this->field2 = $field2;

        return $this;
    }

    public function setTestField4($testField4)
    {
        $this->testField4 = $testField4;

        return $this;
    }

    public function getTestField4()
    {
        return $this->testField4;
    }
}

class TestExtendsEntity extends TestEntity
{
    private $field4;

    public function __construct($arrProps = [])
    {
        if (is_array($arrProps) && count($arrProps) > 0) {
            $this->setPropertiesFromArray($arrProps);
        }
    }

    public function getField4()
    {
        return $this->field4;
    }
}

class TestChildEntity extends TestExtendsEntity
{
    private $TestChildrens;

    public function __construct($arrProps = [])
    {
        $this->TestChildrens = new \Doctrine\Common\Collections\ArrayCollection();
        if (is_array($arrProps) && count($arrProps) > 0) {
            $this->setPropertiesFromArray($arrProps);
        }
    }

    public function setTestChildrens($TestChildrens)
    {
        $this->TestChildrens = $TestChildrens;

        return $this;
    }

    public function getTestChildrens()
    {
        return $this->TestChildrens;
    }

    public function addTestChildren(TestChildren $TestChildren)
    {
        $this->TestChildrens[] = $TestChildren;

        return $this;
    }
}

class TestChildren extends AbstractEntity
{
    /**
     * @Id
     */
    private $childField;

    public function __construct($childField)
    {
        $this->childField = $childField;
    }

    public function getChildField()
    {
        return $this->childField;
    }

    public function setChildField($childField)
    {
        $this->childField = $childField;

        return $this;
    }
}
