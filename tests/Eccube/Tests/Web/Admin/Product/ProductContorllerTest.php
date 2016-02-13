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
            'tag' => $faker->word,
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
        $Product = $this->createProduct();
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

        $this->assertTrue($this->client->getResponse()->isRedirect($this->app->url('admin_product')));

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


}
