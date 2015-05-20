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

class ClassCategoryControllerTest extends AbstractAdminWebTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testRoutingAdminProductClassCategoryCategory()
    {
        $this->client->request('GET',
            $this->app->url('admin_product_class_category', array('class_name_id' => 1))
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminProductClassCategoryEdit()
    {
        $this->client->request('GET',
            $this->app->url('admin_product_class_category_edit',
                array('class_name_id' => 1, 'id' => 1))
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminProductClassCategoryUp()
    {
        $redirectUrl = $this->app->url('admin_product_class_category', array('class_name_id' => 1));

        $this->client->request('POST',
            $this->app->url('admin_product_class_category_up',
                array('class_name_id' => 1, 'id' => 1))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));
    }

    public function testRoutingAdminProductClassCategoryDown()
    {
        $redirectUrl = $this->app->url('admin_product_class_category', array('class_name_id' => 1));

        $this->client->request('POST',
            $this->app->url('admin_product_class_category_down',
                array('class_name_id' => 1, 'id' => 1))
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));
    }

    public function testRoutingAdminProductClassCategoryDelete()
    {
        $redirectUrl = $this->app->url('admin_product_class_category', array('class_name_id' => 1));

        $this->client->request('POST',
            $this->app->url('admin_product_class_category_delete',
                array('class_name_id' => 1, 'id' => 1))
        );

        $actual = $this->client->getResponse()->isRedirect($redirectUrl);
        $this->assertSame(true, $actual);
    }

// TODO: テストデータの作成用メソッドを利用
//    private function newTestClassName()
//    {
//        $TestClassName = new \Eccube\Entity\ClassName();
//        $TestClassName->setName('形状')
//            ->setRank(100)
//            ->setDelFlg(false);
//
//        return $TestClassName;
//    }
//
//    private function newTestClassCategory()
//    {
//        $TestClassName = $this->newTestClassName();
//        $TestClassCategory = new \Eccube\Entity\ClassCategory();
//        $TestClassCategory->setName('立方体')
//            ->setRank(100)
//            ->setClassName($TestClassName)
//            ->setDelFlg(false);
//
//        return $TestClassCategory;
//    }
}