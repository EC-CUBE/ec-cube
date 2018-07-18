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

namespace Eccube\Tests\Twig\Extension;

use Eccube\Common\EccubeConfig;
use Eccube\Service\TaxRuleService;
use Eccube\Twig\Extension\EccubeExtension;
use Eccube\Tests\EccubeTestCase;

class EccubeExtensionTest extends EccubeTestCase
{
    /**
     * @var EccubeExtension
     */
    protected $Extension;

    public function setUp()
    {
        parent::setUp();
        $EccubeConfig = $this->container->get(EccubeConfig::class);
        $TaxRuleService = $this->container->get(TaxRuleService::class);
        $this->Extension = new EccubeExtension($TaxRuleService, $EccubeConfig);
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
                }
                $this->assertEquals(number_format($ProductClass->getPrice02()), $actual['price02']);
                $this->assertEquals(number_format($ProductClass->getPrice02IncTax()), $actual['price02_inc_tax']);
                $this->assertEquals($ProductClass->getCode(), $actual['product_code']);
                $this->assertEquals($ProductClass->getSaleType()->getId(), $actual['sale_type']);
                $this->assertEquals($ProductClass->getStockFind(), $actual['stock_find']);
            }
        }
    }
}
