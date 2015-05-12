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



namespace Eccube\Tests\Web\Admin\SettingShopPaymentController;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class SettingShopPaymentControllerTest extends AbstractAdminWebTestCase
{

// TODO VIewがないため動かない Template "Admin/Basis/payment.twig" is not defined
//    public function testRoutingAdminSettingShopPaymentControllerPayment()
//    {
//        $this->client->request('GET', $this->app['url_generator']
//            ->generate('admin_setting_shop_payment')
//        );
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//    }
//
//    public function testRoutingAdminSettingShopPaymentControllerNew()
//    {
//        $this->client->request('GET', $this->app['url_generator']
//            ->generate('admin_setting_shop_payment_new')
//        );
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//    }
//
//    public function testRoutingAdminSettingShopPaymentControllerEdit()
//    {
//        $this->client->request('GET', $this->app['url_generator']
//            ->generate('admin_setting_shop_payment_edit', array('id' => 0))
//        );
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//    }
//
//    public function testRoutingAdminSettingShopPaymentControllerDelete()
//    {
//        $redirectUrl = $this->app['url_generator']->generate('admin_setting_shop_payment');
//
//        $this->client->request('GET', $this->app['url_generator']
//            ->generate('admin_setting_shop_payment_delete', array('id' => 0))
//        );
//
//        $actual = $this->client->getResponse()->isRedirect($redirectUrl);
//        $this->assertSame(true, $actual);
//    }
//
//    public function testRoutingAdminSettingShopPaymentControllerImage()
//    {
//        $this->client->request('GET', $this->app['url_generator']
//            ->generate('admin_setting_shop_payment_delete_image', array('id' => 0))
//        );
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//    }
//
//    public function testRoutingAdminSettingShopPaymentControllerUp()
//    {
//        $this->client->request('GET', $this->app['url_generator']
//            ->generate('admin_setting_shop_payment_up', array('id' => 0))
//        );
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//    }
//
//    public function testRoutingAdminSettingShopPaymentControllerDown()
//    {
//        $this->client->request('GET', $this->app['url_generator']
//            ->generate('admin_setting_shop_payment_down', array('id' => 0))
//        );
//        $this->assertTrue($this->client->getResponse()->isSuccessful());
//    }

}
