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

namespace Eccube\Tests\Web\Admin\Setting\Shop;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

/**
 * Class ShopControllerTest
 */
class ShopControllerTest extends AbstractAdminWebTestCase
{
    /**
     * Routing
     */
    public function testRouting()
    {
        $this->client->request('GET', $this->generateUrl('admin_setting_shop'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @param bool $isSuccess
     * @param bool $expected
     * @dataProvider dataSubmitProvider
     * @group cache-clear
     */
    public function testSubmit($isSuccess, $expected)
    {
        $formData = $this->createFormData();
        if (!$isSuccess) {
            $formData['shop_name'] = '';
        }
        $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_shop'),
            ['shop_master' => $formData]
        );

        $this->expected = $expected;
        $this->actual = $this->client->getResponse()->isRedirection();
        $this->verify();
    }

    public function createFormData()
    {
        $delivery_free_amount = 1000;
        if (mt_rand(0, 1)) {
            $delivery_free_amount = number_format($delivery_free_amount);
        }

        $form = [
            '_token' => 'dummy',
            'company_name' => '会社名',
            'company_kana' => 'カナ',
            'shop_name' => '店舗名',
            'shop_kana' => 'カナ',
            'shop_name_eng' => 'shopname',
            'zip' => [
                'zip01' => '530',
                'zip02' => '0001',
            ],
            'address' => [
                'pref' => '5',
                'addr01' => '北区',
                'addr02' => '梅田',
            ],
            'tel' => [
                'tel01' => '031',
                'tel02' => '111',
                'tel03' => '1111',
            ],
            'fax' => [
                'fax01' => '031',
                'fax02' => '111',
                'fax03' => '4444',
            ],
            'business_hour' => '店舗営業時間',
            'email01' => 'eccube@example.com',
            'email02' => 'eccube@example.com',
            'email03' => 'eccube@example.com',
            'email04' => 'eccube@example.com',
            'delivery_free_amount' => $delivery_free_amount,
            'delivery_free_quantity' => '1000',
            'good_traded' => '取り扱い商品',
            'message' => 'メッセージ',
            'option_product_delivery_fee' => '0',
            'option_customer_activate' => '0',
            'option_mypage_order_status_display' => '0',
            'option_favorite_product' => 0,
            'option_remember_me' => '0',
            'option_nostock_hidden' => '0',
            'option_point' => 1,
            'basic_point_rate' => 1,
        ];

        return $form;
    }

    public function dataSubmitProvider()
    {
        return [
            [false, false],
            [true, true],
            // To do implement
        ];
    }
}
