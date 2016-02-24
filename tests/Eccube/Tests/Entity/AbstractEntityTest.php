<?php

namespace Eccube\Tests\Entity;

use Eccube\Entity\AbstractEntity;

/**
 * AbstractEntity test cases.
 *
 * @author Kentaro Ohkouchi
 */
class AbstractEntityTest extends \PHPUnit_Framework_TestCase
{
    private $objEntity;

    public function testNewInstance()
    {
        $arrProps = array(
            'field1' => 1,
            'field2' => 2,
            'field3' => 3,
            'testField4' => 4
        );
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
        $arrProps = array(
            'field1' => 1,
            'field2' => 2,
            'field3' => 3,
            'testField4' => 4
        );
        $this->objEntity = new TestEntity($arrProps);
        $expected = $arrProps;
        $actual = $this->objEntity->toArray();
        $this->assertEquals($expected, $actual);
    }

    public function testSetPropertiesFromArray()
    {
        $arrProps = array(
            'field2' => null,
            'field3' => 3,
            'field4' => 4,
            'testField4' => 5,
            'fieldXXX' => 'XXX'
        );

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
        $arrProps = array(
            'field1' => 1,
            'field2' => 2,
            'field3' => 3,
            'testField4' => 4
        );
        $this->objEntity = new TestEntity($arrProps);
        $this->assertEquals($this->objEntity->getField1(), 1);
        $this->assertEquals($this->objEntity->getField2(), 2);
        $this->assertEquals($this->objEntity->field3, 3);
        $this->assertEquals($this->objEntity->getTestField4(), 4);
    }

    public function testExtends()
    {
        $arrProps = array(
            'field1' => 1,
            'field2' => 2,
            'field3' => 3,
            'field4' => 4,
            'testField4' => 5
        );
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
        $TestChildrens = new \Doctrine\Common\Collections\ArrayCollection();
        $TestChildrens[] = new TestChildren('child1');
        $TestChildrens[] = new TestChildren('child2');
        $TestChildrens[] = new TestChildren('child3');
        $arrProps = array(
            'field1' => 1,
            'field2' => 2,
            'field3' => 3,
            'field4' => 4,
            'testField4' => 5,
            'TestChildrens' => $TestChildrens
        );

        $this->objEntity = new TestChildEntity($arrProps);
        $this->assertEquals($this->objEntity->getField1(), 1);
        $this->assertEquals($this->objEntity->getField2(), 2);
        $this->assertEquals($this->objEntity->field3, 3);
        $this->assertEquals($this->objEntity->getField4(), 4);
        $this->assertEquals($this->objEntity->getTestField4(), 5);
        $expected = $arrProps;
        $actual = $this->objEntity->toArray();
        $this->assertEquals($expected, $actual);
    }

    public function testCopyProperties()
    {
        $arrProps = array(
            'field1' => 1,
            'field2' => 2,
            'field3' => 3,
            'field4' => 4,
            'testField4' => 5
        );
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
        $arrProps = array(
            'field1' => 1,
            'field2' => 2,
            'field3' => 3,
            'field4' => 4,
            'testField4' => 5
        );
        $srcEntity = new TestExtendsEntity($arrProps);
        $destEntity = new TestExtendsEntity();

        $destEntity->copyProperties($srcEntity, array('field1')); // field1 は除外
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
        $arrProps = array(
            'field1' => 1,
            'field2' => null,
            'field3' => 3,
            'field4' => 4,
            'testField4' => 5,
            'fieldXXX' => 'XXX'
        );
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

    public function __construct($arrProps = array())
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

    public function __construct($arrProps = array())
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

    public function __construct($arrProps = array())
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

class TestChildren
{
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
