<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Tests\Web\Admin\Product;

use Eccube\Common\Constant;
use Eccube\Entity\TaxRule;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class ProductControllerTest extends AbstractAdminWebTestCase
{

    public function createFormData()
    {
        $faker = $this->getFaker();
        $form = array(
            'class' => array(
                'product_type' => 1,
                'price01' => $faker->randomNumber(5),
                'price02' => $faker->randomNumber(5),
                'stock' => $faker->randomNumber(3),
                'stock_unlimited' => 0,
                'code' => $faker->word,
                'sale_limit' => null,
                'delivery_date' => ''
            ),
            'name' => $faker->word,
            'product_image' => null,
            'description_detail' => $faker->text,
            'description_list' => $faker->paragraph,
            'Category' => null,
            'Tag' => 1,
            'search_word' => $faker->word,
            'free_area' => $faker->text,
            'Status' => 1,
            'note' => $faker->text,
            'tags' => null,
            'images' => null,
            'add_images' => null,
            'delete_images' => null,
            '_token' => 'dummy',
        );
        return $form;
    }

    public function testRoutingAdminProductProduct()
    {
        $this->client->request('GET',
            $this->app->url('admin_product')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminProductProductNew()
    {
        $this->client->request('GET',
            $this->app->url('admin_product_product_new')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminProductProductEdit()
    {

        $TestProduct = $this->createProduct();

        $test_product_id = $this->app['eccube.repository.product']
            ->findOneBy(array(
                'name' => $TestProduct->getName()
            ))
            ->getId();

        $crawler = $this->client->request('GET',
            $this->app->url('admin_product_product_edit', array('id' => $test_product_id))
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testEditWithPost()
    {
        $Product = $this->createProduct(null, 0);
        $formData = $this->createFormData();
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_product_product_edit', array('id' => $Product->getId())),
            array('admin_product' => $formData)
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_product_product_edit', array('id' => $Product->getId()))));

        $EditedProduct = $this->app['eccube.repository.product']->find($Product->getId());
        $this->expected = $formData['name'];
        $this->actual = $EditedProduct->getName();
        $this->verify();
    }

    public function testDelete()
    {
        $Product = $this->createProduct();
        $crawler = $this->client->request(
            'DELETE',
            $this->app->url('admin_product_product_delete', array('id' => $Product->getId()))
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_product_page', array('page_no' => 1)).'?resume=1'));

        $DeletedProduct = $this->app['eccube.repository.product']->find($Product->getId());
        $this->expected = 1;
        $this->actual = $DeletedProduct->getDelFlg();
        $this->verify();
    }

    public function testCopy()
    {
        $Product = $this->createProduct();
        $AllProducts = $this->app['eccube.repository.product']->findAll();
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_product_product_copy', array('id' => $Product->getId()))
        );

        $this->assertTrue($this->client->getResponse()->isRedirect());

        $AllProducts2 = $this->app['eccube.repository.product']->findAll();
        $this->expected = count($AllProducts) + 1;
        $this->actual = count($AllProducts2);
        $this->verify();
    }

    private function newTestProduct($TestCreator)
    {
        $TestProduct = new \Eccube\Entity\Product();
        $Disp = $this->app['orm.em']->getRepository('Eccube\Entity\Master\Disp')->find(1);
        $TestProduct->setName('テスト商品')
            ->setStatus($Disp)
            ->setNote('test note')
            ->setDescriptionList('テスト商品 商品説明(リスト)')
            ->setDescriptionDetail('テスト商品 商品説明(詳細)')
            ->setFreeArea('フリー記載')
            ->setDelFlg(0)
            ->setCreator($TestCreator);

        return $TestProduct;
    }



    private function newTestProductClass($TestCreator, $TestProduct)
    {
        $TestClassCategory = new \Eccube\Entity\ProductClass();
        $ProductType = $this->app['orm.em']
            ->getRepository('\Eccube\Entity\Master\ProductType')
            ->find(1);
        $TestClassCategory->setProduct($TestProduct)
            ->setProductType($ProductType)
            ->setCode('test code')
            ->setStock(100)
            ->setStockUnlimited(0)
//            ->setDeliveryDateId(1)
            ->setSaleLimit(10)
            ->setPrice01(10000)
            ->setPrice02(5000)
            ->setDeliveryFee(1000)
            ->setCreator($TestCreator)
            ->setDelFlg(0);
        return $TestClassCategory;
    }


    private function newTestProductStock($TestCreator, $TestProduct, $TestProductClass)
    {
        $TestProductStock = new \Eccube\Entity\ProductStock();
        $TestProductClass->setProductStock($TestProductStock);
        $TestProductStock->setProductClass($TestProductClass);
        $TestProductStock->setStock($TestProductClass->getStock());
        $TestProductStock->setCreator($TestCreator);
        return $TestProductStock;
    }

    /**
     * @param $taxRate
     * @param $expected
     * @dataProvider dataNewProductProvider
     */
    public function testNewWithPostTaxRate($taxRate, $expected)
    {
        // Give
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionProductTaxRule(Constant::ENABLED);
        $formData = $this->createFormData();

        $formData['class']['tax_rate'] = $taxRate;
        // When
        $this->client->request(
            'POST',
            $this->app->url('admin_product_product_new'),
            array('admin_product' => $formData)
        );

        // Then
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $arrTmp = explode('/', $this->client->getResponse()->getTargetUrl());
        $productId = $arrTmp[count($arrTmp)-2];
        $Product = $this->app['eccube.repository.product']->find($productId);

        $this->expected = $expected;
        $Taxrule = $this->app['eccube.repository.tax_rule']->findOneBy(array('Product' => $Product));
        $taxRate = is_null($taxRate) ? null : $Taxrule->getTaxRate();
        $this->actual = $taxRate;
        $this->assertTrue($this->actual === $this->expected);
    }

    public function dataNewProductProvider()
    {
        return array(
            array(null, null),
            array("0", "0"),
            array("1", "1"),
        );
    }

    /**
     * 個別税率設定のテストケース
     * 個別税率設定を有効にし、商品編集時に更新されることを確認する
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1547
     * @param $before 更新前の税率
     * @param $after POST値
     * @param $expected 期待値
     *
     * @dataProvider dataEditProductProvider
     */
    public function testEditWithPostTaxRate($before, $after, $expected)
    {
        // Give
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionProductTaxRule(Constant::ENABLED);
        $Product = $this->createProduct(null, 0);
        $ProductClasses = $Product->getProductClasses();
        $ProductClass = $ProductClasses[0];
        $formData = $this->createFormData();

        if (!is_null($after)) {
            $formData['class']['tax_rate'] = $after;
        }
        if (!is_null($before)) {
            $DefaultTaxRule = $this->app['eccube.repository.tax_rule']->find(\Eccube\Entity\TaxRule::DEFAULT_TAX_RULE_ID);

            $TaxRule = new TaxRule();
            $TaxRule->setProductClass($ProductClass)
                ->setCreator($Product->getCreator())
                ->setProduct($Product)
                ->setCalcRule($DefaultTaxRule->getCalcRule())
                ->setTaxRate($before)
                ->setTaxAdjust(0)
                ->setApplyDate(new \DateTime())
                ->setDelFlg(Constant::DISABLED);
            $ProductClass->setTaxRule($TaxRule);
            $this->app['orm.em']->persist($TaxRule);
            $this->app['orm.em']->flush();
        }

        // When
        $this->client->request(
            'POST',
            $this->app->url('admin_product_product_edit', array('id' => $Product->getId())),
            array('admin_product' => $formData)
        );

        // Then
        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_product_product_edit', array('id' => $Product->getId()))));

        $this->expected = $expected;
        $TaxRule = $this->app['eccube.repository.tax_rule']->findOneBy(array('Product' => $Product, 'ProductClass' => $ProductClass));

        if (is_null($TaxRule)) {
            $this->actual = null;
        } else {
            $this->actual = $TaxRule->getTaxRate();
        }

        $this->assertTrue($this->actual === $this->expected);
    }

    /**
     * 個別税率編集時のテストデータ
     * 更新前の税率 / POST値 / 期待値の配列を返す
     *
     * @return array
     */
    public function dataEditProductProvider()
    {
        return array(
            array('0', '0', '0'),
            array('0', '1', '1'),
            array('0', null, null),
            array('1', '0', '0'),
            array('1', '1', '1'),
            array('1', null, null),
            array(null, '0', '0'),
            array(null, '1', '1'),
            array(null, null, null),
        );
    }
}
