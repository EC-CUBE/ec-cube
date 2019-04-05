<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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


namespace Eccube\Tests\Plugin\Web;

use Eccube\Common\Constant;
use Eccube\Event\EccubeEvents;

class ProductControllerTest extends AbstractWebTestCase
{

    public function testRoutingList()
    {
        $client = $this->client;
        $client->request('GET', $this->app->url('product_list'));
        $this->assertTrue($client->getResponse()->isSuccessful());

        $hookpoins = array(
            EccubeEvents::FRONT_PRODUCT_INDEX_INITIALIZE,
            EccubeEvents::FRONT_PRODUCT_INDEX_SEARCH,
            EccubeEvents::FRONT_PRODUCT_INDEX_DISP,
            EccubeEvents::FRONT_PRODUCT_INDEX_ORDER,
        );
        $this->verifyOutputString($hookpoins);
    }

    public function testRoutingDetail()
    {
        $client = $this->client;
        $client->request('GET', $this->app->url('product_detail', array('id' => '1')));
        $this->assertTrue($client->getResponse()->isSuccessful());

        $hookpoins = array(
            EccubeEvents::FRONT_PRODUCT_DETAIL_INITIALIZE,
        );
        $this->verifyOutputString($hookpoins);
    }

    public function testRoutingProductAdd()
    {

        // データ整備後にテストを実施すること
        $this->markTestSkipped();

        // お気に入り商品機能を有効化
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionFavoriteProduct(Constant::ENABLED);

        $this->logIn();
        $client = $this->client;

        /** @var \Eccube\Entity\Product $Product */
        $Product = $this->app['eccube.repository.product']->find(1);

        $ProductClasses = $Product->getProductClasses();
        $categories1  = $Product->getClassCategories1();
        $categories2  = $Product->getClassCategories2($categories1[0]);


        $form = $this->createFormData();
        $client->request(
            'POST',
            $this->app->url('product_detail', array('id' => '1')),
            $form
        );

        $hookpoins = array(
            EccubeEvents::FRONT_PRODUCT_DETAIL_INITIALIZE,
            EccubeEvents::FRONT_PRODUCT_DETAIL_COMPLETE,
        );
        $this->verifyOutputString($hookpoins);
    }

    public function testRoutingProductFavoriteAdd()
    {

        // データ整備後にテストを実施すること
        $this->markTestSkipped();

        // お気に入り商品機能を有効化
        $BaseInfo = $this->app['eccube.repository.base_info']->get();
        $BaseInfo->setOptionFavoriteProduct(Constant::ENABLED);

        $this->logIn();
        $client = $this->client;

        $form = $this->createFormData2();
        $client->request(
            'POST',
            $this->app->url('product_detail', array('id' => '1')),
            $form
        );

        $hookpoins = array(
            EccubeEvents::FRONT_PRODUCT_DETAIL_INITIALIZE,
            EccubeEvents::FRONT_PRODUCT_DETAIL_FAVORITE,
        );
        $this->verifyOutputString($hookpoins);
    }

    protected function createFormData()
    {

        $form = array(
            'mode' => null,
            'product_id' => 1,
            'product_class_id' => 1,
            'quantity' => 1,
            //'classcategory_id1' => 3,
            //'classcategory_id2' => 6,
            '_token' => 'dummy',
        );
        return $form;
    }

    protected function createFormData2()
    {

        $form = array(
            'mode' => 'add_favorite',
            'product_id' => 1,
            'product_class_id' => 1,
            'quantity' => 1,
            '_token' => 'dummy',
        );
        return $form;
    }
}
