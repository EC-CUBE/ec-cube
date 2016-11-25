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


namespace Eccube\Tests\Web\Admin\Setting\Shop;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

/**
 * Class ShopControllerTest
 * @package Eccube\Tests\Web\Admin\Setting\Shop
 */
class ShopControllerTest extends AbstractAdminWebTestCase
{
    /**
     * Routing
     */
    public function testRouting()
    {
        $this->client->request('GET', $this->app->url('admin_setting_shop'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @param bool $isSuccess
     * @param bool $expected
     * @dataProvider dataSubmitProvider
     */
    public function testSubmit($isSuccess, $expected)
    {
        $formData = $this->createFormData();
        if (!$isSuccess) {
            $formData['shop_name'] = '';
        }
        $this->client->request(
            'POST',
            $this->app->url('admin_setting_shop'),
            array('shop_master' => $formData)
        );

        $this->expected = $expected;
        $this->actual = $this->client->getResponse()->isRedirection();
        $this->verify();
    }

    public function createFormData()
    {
        $form = array(
            '_token' => 'dummy',
            'company_name' => '会社名',
            'company_kana' => 'カナ',
            'shop_name' => '店舗名',
            'shop_kana' => 'カナ',
            'shop_name_eng' => 'shopname',
            'zip' => array(
                'zip01' => '530',
                'zip02' => '0001',
            ),
            'address' => array(
                'pref' => '5',
                'addr01' => '北区',
                'addr02' => '梅田',
            ),
            'tel' => array(
                'tel01' => '031',
                'tel02' => '111',
                'tel03' => '1111',
            ),
            'fax' => array(
                'fax01' => '031',
                'fax02' => '111',
                'fax03' => '4444',
            ),
            'business_hour' => '店舗営業時間',
            'email01' => 'eccube@example.com',
            'email02' => 'eccube@example.com',
            'email03' => 'eccube@example.com',
            'email04' => 'eccube@example.com',
            'delivery_free_amount' => '1000',
            'delivery_free_quantity' => '1000',
            'good_traded' => '取り扱い商品',
            'message' => 'メッセージ',
            'option_product_delivery_fee' => '0',
            'option_multiple_shipping' => '0',
            'option_customer_activate' => '0',
            'option_mypage_order_status_display' => '0',
            'option_favorite_product' => 0,
            'option_remember_me' => '0',
            'nostock_hidden' => '0',
            'latitude' => '',
            'longitude' => '',
        );

        return $form;
    }

    public function dataSubmitProvider()
    {
        return array(
            array(false, false),
            array(true, true),
            // To do implement
        );
    }
}
