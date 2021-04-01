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

namespace Eccube\Tests\Twig\Extension;

use Eccube\Common\EccubeConfig;
use Eccube\Tests\EccubeTestCase;
use Eccube\Twig\Extension\EccubeExtension;

class EccubeExtensionTest extends EccubeTestCase
{
    /**
     * @var EccubeExtension
     */
    protected $Extension;

    public function setUp()
    {
        parent::setUp();
        $EccubeConfig = self::$container->get(EccubeConfig::class);
        $productRepository = $this->entityManager->getRepository(\Eccube\Entity\Product::class);
        $this->Extension = new EccubeExtension($EccubeConfig, $productRepository);
    }

    public function testGetClassCategoriesAsJson()
    {
        $faker = $this->getFaker();
        $Product = $this->createProduct($faker->word, 3);

        $actuals = json_decode($this->Extension->getClassCategoriesAsJson($Product), true);

        foreach ($Product->getClassCategories1() as $class_category_id => $name) {
            $this->assertArrayHasKey($class_category_id, $actuals);

            $ClassCategory2 = $Product->getClassCategories2($class_category_id);
            if (empty($ClassCategory2)) {
                $this->markTestSkipped('ClassCategory2 is empty.');
            }

            foreach ($ClassCategory2 as $class_category_id2 => $name2) {
                $this->assertArrayHasKey('#'.$class_category_id2, $actuals[$class_category_id]);

                $actual = $actuals[$class_category_id]['#'.$class_category_id2];

                $this->assertEquals($class_category_id2, $actual['classcategory_id2']);
                $this->assertEquals($name2, $actual['name']);

                $ProductClass = $Product
                    ->getProductClasses()
                    ->filter(
                        function ($ProductClass) use ($actual) {
                            return $ProductClass->getId() == $actual['product_class_id'];
                        })
                    ->first();

                if ($ProductClass->getPrice01IncTax()) {
                    $this->assertEquals(number_format($ProductClass->getPrice01()), $actual['price01']);
                    $this->assertEquals(number_format($ProductClass->getPrice01IncTax()), $actual['price01_inc_tax']);
                    $this->assertEquals($this->Extension->getPriceFilter($ProductClass->getPrice01()), $actual['price01_with_currency']);
                    $this->assertEquals($this->Extension->getPriceFilter($ProductClass->getPrice01IncTax()), $actual['price01_inc_tax_with_currency']);
                }
                $this->assertEquals(number_format($ProductClass->getPrice02()), $actual['price02']);
                $this->assertEquals(number_format($ProductClass->getPrice02IncTax()), $actual['price02_inc_tax']);
                $this->assertEquals($this->Extension->getPriceFilter($ProductClass->getPrice02()), $actual['price02_with_currency']);
                $this->assertEquals($this->Extension->getPriceFilter($ProductClass->getPrice02IncTax()), $actual['price02_inc_tax_with_currency']);
                $this->assertEquals($ProductClass->getCode(), $actual['product_code']);
                $this->assertEquals($ProductClass->getSaleType()->getId(), $actual['sale_type']);
                $this->assertEquals($ProductClass->getStockFind(), $actual['stock_find']);
            }
        }
    }

    /**
     * @dataProvider extensionProvider
     */
    public function testGetExtensionIcon($ext, $iconOnly, $expected)
    {
        $actual = $this->Extension->getExtensionIcon($ext, [], $iconOnly);
        $this->assertEquals($expected, $actual);
    }

    public function extensionProvider()
    {
        return [
            ['jpg', false, '<i class="fa fa-file-image-o" ></i>'],
            ['JPG', false, '<i class="fa fa-file-image-o" ></i>'],
            ['jpg', true, 'fa-file-image-o'],
            ['JPG', true, 'fa-file-image-o'],
        ];
    }
}
