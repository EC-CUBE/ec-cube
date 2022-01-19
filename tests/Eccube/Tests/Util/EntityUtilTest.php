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

namespace Eccube\Tests\Util;

use Eccube\Entity\AbstractEntity;
use Eccube\Tests\EccubeTestCase;
use Eccube\Util\EntityUtil;

/**
 * EntityUtil test cases.
 *
 * @author Kentaro Ohkouchi
 */
class EntityUtilTest extends EccubeTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testDumpToArray()
    {
        $arrProps = [
            'field1' => 1,
            'field2' => 2,
            'field3' => 3,
        ];

        $entity = new TestEntity($arrProps);

        $arrProps['testField4'] = 'Doctrine\Common\Collections\ArrayCollection';
        $this->expected = $arrProps;
        $this->actual = EntityUtil::dumpToArray($entity);
        $this->verify();
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
        $this->testField4 = new \Doctrine\Common\Collections\ArrayCollection();
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
