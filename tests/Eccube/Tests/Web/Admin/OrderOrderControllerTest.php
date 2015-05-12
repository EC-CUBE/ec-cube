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

namespace Eccube\Tests\Web\Admin\OrderOrderController;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class OrderOrderControllerTest extends AbstractAdminWebTestCase
{


    public function testRoutingAdminOrderOrderControllerOrder()
    {
        $this->client->request('GET', $this->app['url_generator']
            ->generate('admin_order')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

// TODO 削除後リダイレクトされない
//    public function testRoutingAdminOrderOrderControllerDelete()
//    {
//        $redirectUrl = $this->app['url_generator']->generate('admin_order');
//
//        $this->client->request('GET', $this->app['url_generator']
//            ->generate('admin_order_delete', array('id' => 0))
//        );
//
//        $actual = $this->client->getResponse()->isRedirect($redirectUrl);
//        $this->assertSame(true, $actual);
//    }

// TODO 未実装
//    public function testRoutingAdminOrderOrderControllerRecalc()
//    {
//        $this->client->request('GET', $this->app['url_generator']
//            ->generate('admin_order_recalc', array('id' => 0))
//        );
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//    }
//
//    public function testRoutingAdminOrderOrderControllerProductAdd()
//    {
//        $this->client->request('GET', $this->app['url_generator']
//            ->generate('admin_order_product_add', array('id' => 0, 'shop_id' => 0))
//        );
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//    }
//
//
//    public function testRoutingAdminOrderOrderControllerProductSelect()
//    {
//        $this->client->request('GET', $this->app['url_generator']
//            ->generate('admin_order_product_select', array('id' => 0, 'shop_id' => 0))
//        );
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//    }
//
//    public function testRoutingAdminOrderOrderControllerProductDelete()
//    {
//        $redirectUrl = $this->app['url_generator']->generate('admin_order_product');
//
//        $this->client->request('GET', $this->app['url_generator']
//            ->generate('admin_order_product_delete', array('id' => 0, 'shop_id' => 0))
//        );
//
//        $actual = $this->client->getResponse()->isRedirect($redirectUrl);
//        $this->assertSame(true, $actual);
//    }
//
//    public function testRoutingAdminOrderOrderControllerAdd()
//    {
//        $this->client->request('GET', $this->app['url_generator']
//            ->generate('admin_order_shipping_add')
//        );
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//    }

}

