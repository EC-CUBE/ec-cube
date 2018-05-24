<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Util;

use Eccube\Entity\AbstractEntity;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Tests\EccubeTestCase;
use Eccube\Util\EntityUtil;

/**
 * EntityUtil test cases.
 *
 * @author Kentaro Ohkouchi
 */
class EntityUtilTest extends EccubeTestCase
{
    private $Product;
    private $ProductClass;

    public function setUp()
    {
        parent::setUp();
        // eccube_install.sh で追加される Member
        $Member = $this->entityManager->find(\Eccube\Entity\Member::class, 1);

        $Product = new Product();
        $ProductClass = new ProductClass();
        $ProductStatus = $this->entityManager->find(\Eccube\Entity\Master\ProductStatus::class,
            \Eccube\Entity\Master\ProductStatus::DISPLAY_HIDE);
        $SaleType = $this->entityManager->find(\Eccube\Entity\Master\SaleType::class, 1);
        $Product
            ->setName('test')
            ->setCreator($Member)
            ->addProductClass($ProductClass)
            ->setStatus($ProductStatus);
        $ProductClass
            ->setPrice02(1000)
            ->setCreator($Member)
            ->setVisible(true)
            ->setStockUnlimited(true)
            ->setSaleType($SaleType)
            ->setProduct($Product);
        $ProductStock = new \Eccube\Entity\ProductStock();
        $ProductStock->setCreator($Member);
        $ProductClass->setProductStock($ProductStock);
        $ProductStock->setProductClass($ProductClass);

        $this->entityManager->persist($Product);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->persist($ProductStock);
        $this->entityManager->flush();

        $this->Product = $Product;
        $this->ProductClass = $ProductClass;
    }

    public function testIsEmptyWithFalse()
    {
        // setUp() で追加したサンプル商品
        $Product = $this->entityManager->find(Product::class, $this->Product->getId());
        // eccube_install.sh で追加される Member
        $Member = $Product->getCreator();
        /*
         * member.del_flg = 0 になっているので、soft_delete filter が適用されず
         * LAZY loading で取得できる
         */
        $this->assertFalse(EntityUtil::isEmpty($Member));
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
