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

namespace Eccube\Tests\Form\Type\Admin;

use Eccube\Form\Type\Admin\SecurityType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class SecurityTypeTest extends AbstractTypeTestCase
{
    /**
     * @var \Symfony\Component\Form\FormInterface
     */
    protected $form;

    /**
     * @var array デフォルト値（正常系）を設定
     */
    protected $formData = [
        'admin_route_dir' => 'admin',
        'admin_allow_hosts' => '',
        'admin_deny_hosts' => '',
        'front_allow_hosts' => '',
        'front_deny_hosts' => '',
        'trusted_hosts' => 'localhost',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(SecurityType::class, null, ['csrf_protection' => false])
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidAdminAllowHostOneLineIp()
    {
        $this->formData['admin_allow_hosts'] = '127.0.0.1';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidAdminAllowHostMultiLineIps()
    {
        $this->formData['admin_allow_hosts'] = "127.0.0.1\n1.1.1.1";
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidAdminAllowHostNotIp()
    {
        $this->formData['admin_allow_hosts'] = '255.255.255,256';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidAdminDenyHostOneLineIp()
    {
        $this->formData['admin_deny_hosts'] = '127.0.0.1';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidAdminDenyHostMultiLineIps()
    {
        $this->formData['admin_deny_hosts'] = "127.0.0.1\n1.1.1.1";
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidAdminDenyHostNotIp()
    {
        $this->formData['admin_deny_hosts'] = '255.255.255,256';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    /**
     * Over ltext_len = 3000
     */
    public function testValidAdminAllowHostMaxLength()
    {
        $this->formData['admin_allow_host'] = str_repeat("127.0.0.1\n", 1000);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    /**
     * @dataProvider adminRouteDirParams
     */
    public function testAdminRouteDir($rootDir, $valid)
    {
        $this->formData['admin_route_dir'] = $rootDir;
        $this->form->submit($this->formData);
        $this->assertEquals($valid, $this->form->isValid());
    }

    public function adminRouteDirParams()
    {
        return [
            ['admin', true],
            ['ADMIN', true],
            ['12345', true],
            ['adminADMIN123', true],
            ['admin_admin', true],
            ['/admin', false],
            ['admin/', false],
            ['admin/route', false],
            ['admin&', false],
            ['admin?', false],
            ['/admin/content/news/page/{page_no}', false],
            ['/admin/disable_maintenance/{mode}', false],
            ['/admin/content/news/page/{page_no}', false],
            ['/admin/product/class_category/{class_name_id}/{id}/edit', false],
            ['cart_admin', true],
            ['admin_cart', true],
            ['product_admin', true],
            ['admin_products', true],
            ['cart', false],
            ['cart&', false],
            ['cart?', false],
            ['/cart', false],
            ['/cart/', false],
            ['/cart/buystep', false],
            ['/cart/buystep&', false],
            ['/cart/buystep?', false],
            ['/cart/buystep/', false],
            ['/cart/buystep/{cart_key}', false],
            ['/cart/{operation}/{productClassId}', false],
            ['contact', false],
            ['contact&', false],
            ['contact?', false],
            ['/contact', false],
            ['/contact/', false],
            ['/contact/complete', false],
            ['/contact/complete&', false],
            ['/contact/complete?', false],
            ['entry', false],
            ['entry?', false],
            ['entry&', false],
            ['/entry', false],
            ['/entry&', false],
            ['/entry?', false],
            ['/entry/', false],
            ['/entry/complete', false],
            ['/entry/complete&', false],
            ['/entry/complete?', false],
            ['/entry/activate', false],
            ['/entry/activate?', false],
            ['/entry/activate&', false],
            ['/entry/activate/', false],
            ['/entry/activate/{secret_key}/{qtyInCart}', false],
            ['/forgot', false],
            ['/forgot&', false],
            ['/forgot?', false],
            ['/forgot/complete', false],
            ['/forgot/complete?', false],
            ['/forgot/complete&', false],
            ['/forgot/reset', false],
            ['/forgot/reset&', false],
            ['/forgot/reset?', false],
            ['/forgot/reset/', false],
            ['/forgot/reset/{reset_key}', false],
            ['/help/tradelaw', false],
            ['/help/tradelaw&', false],
            ['/help/tradelaw?', false],
            ['/guide', false],
            ['/guide&', false],
            ['/guide?', false],
            ['/help/about', false],
            ['/help/about&', false],
            ['/help/about?', false],
            ['/help/privacy', false],
            ['/help/privacy&', false],
            ['/help/privacy?', false],
            ['/help/agreement', false],
            ['/help/agreement&', false],
            ['/help/agreement?', false],
            ['/install/plugins', false],
            ['/install/plugins&', false],
            ['/install/plugins?', false],
            ['/install/plugin', false],
            ['/install/plugin&', false],
            ['/install/plugin?', false],
            ['/install/plugin/', false],
            ['/install/plugin/redirect', false],
            ['/install/plugin/redirect?', false],
            ['/install/plugin/redirect&', false],
            ['/install/plugin/{code}/enable', false],
            ['/install', false],
            ['/install?', false],
            ['/install&', false],
            ['/install/', false],
            ['/install/step1', false],
            ['/install/step1?', false],
            ['/install/step1&', false],
            ['/install/step1/', false],
            ['/install/step2', false],
            ['/install/step2?', false],
            ['/install/step2&', false],
            ['/install/step2/', false],
            ['/install/step3', false],
            ['/install/step3?', false],
            ['/install/step3&', false],
            ['/install/step3/', false],
            ['/install/step4', false],
            ['/install/step4?', false],
            ['/install/step4&', false],
            ['/install/step4/', false],
            ['/install/step5', false],
            ['/install/step5?', false],
            ['/install/step5&', false],
            ['/install/step5/', false],
            ['/install/complete', false],
            ['/install/complete?', false],
            ['/install/complete&', false],
            ['/mypage/change', false],
            ['/mypage/change?', false],
            ['/mypage/change&', false],
            ['/mypage/change/', false],
            ['/mypage/change_complete', false],
            ['/mypage/change_complete?', false],
            ['/mypage/change_complete&', false],
            ['/mypage/change_complete/', false],
            ['/mypage/delivery', false],
            ['/mypage/delivery?', false],
            ['/mypage/delivery&', false],
            ['/mypage/delivery/', false],
            ['/mypage/delivery/new', false],
            ['/mypage/delivery/new?', false],
            ['/mypage/delivery/new&', false],
            ['/mypage/delivery/new/', false],
            ['/mypage/delivery/{id}/edit', false],
            ['/mypage/login', false],
            ['/mypage/login?', false],
            ['/mypage/login&', false],
            ['/mypage/login/', false],
            ['/mypage/', false],
            ['/mypage/history', false],
            ['/mypage/history?', false],
            ['/mypage/history&', false],
            ['/mypage/history/', false],
            ['/mypage/order', false],
            ['/mypage/order?', false],
            ['/mypage/order&', false],
            ['/mypage/order/', false],
            ['/mypage/order/{order_no}', false],
            ['/mypage/favorite', false],
            ['/mypage/withdraw', false],
            ['/mypage/withdraw', false],
            ['/mypage/withdraw_complete', false],
            ['/shopping/nonmember', false],
            ['/shopping/customer', false],
            ['products', false],
            ['products?', false],
            ['products&', false],
            ['/products', false],
            ['/products/list', false],
            ['/products/detail/{id}', false],
            ['/products/add_favorite/{id}', false],
            ['/products/add_cart/{id}', false],
            ['/shopping/shipping_multiple', false],
            ['/shopping/shipping_multiple_edit', false],
            ['/shopping/shipping/{id}', false],
            ['/shopping', false],
            ['/shopping/redirect_to', false],
            ['/shopping/confirm', false],
            ['/shopping/checkout', false],
            ['/shopping/complete', false],
            ['/shopping/login', false],
            ['/shopping/error', false],
            ['/', false],
            ['/logout', false],
            ['/sitemap.xml', false],
            ['/sitemap_category.xml', false],
            ['/sitemap_product_{page}.xml', false],
            ['/sitemap_page.xml', false],
            ['/user_data/{route}', false],
        ];
    }

    public function testTrustedHosts()
    {
        $this->formData['trusted_hosts'] = '^127\.0\.0.1$,^localhost$';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function ipAddressParams()
    {
        return [
            // 正常系（適切なIPアドレス表記として認める）
            ['', true], // 空パターン
            ['127.0.0.1', true], // IPアドレスのみ
            ['192.168.56.1/0', true], // IPアドレスとビットマスク最小値
            ['192.168.56.1/32', true], // IPアドレスとビットマスク最大値
            ["127.0.0.1\n192.168.56.1/32", true], // 複数行に渡る記述
            [str_repeat("127.0.0.1\n", 300), true], // 300回リピート（3000byte以内チェック）

            // 異常系（IPアドレス表記として認めないパターン）
            ['a', false], // 表記に従わない記述
            ['192.168.56.1/33', false], // ビットマスク最大値を超えた値
            ['192.168.56.1/a', false], // ビットマスクが不正な値
            ["127.0.0.1\n192.168.56.1/33", false], // 複数行に渡る記述で2行目が不正な値
            ['999.168.56.1/32', false], // IPアドレスの範囲外
            [str_repeat("127.0.0.1\n", 301), false], // 301回リピート（3000byteオーバーチェック）
        ];
    }

    /**
     * @dataProvider ipAddressParams
     */
    public function testFrontAllowHost($ip, $valid)
    {
        $this->formData['front_allow_hosts'] = $ip;
        $this->form->submit($this->formData);
        $this->assertSame($valid, $this->form['front_allow_hosts']->isValid());
    }

    /**
     * @dataProvider ipAddressParams
     */
    public function testFrontDenyHost($ip, $valid)
    {
        $this->formData['front_deny_hosts'] = $ip;
        $this->form->submit($this->formData);
        $this->assertSame($valid, $this->form['front_deny_hosts']->isValid());
    }


    /**
     * @dataProvider ipAddressParams
     */
    public function testAdminAllowHost($ip, $valid)
    {
        $this->formData['admin_allow_hosts'] = $ip;
        $this->form->submit($this->formData);
        $this->assertSame($valid, $this->form['admin_allow_hosts']->isValid());
    }

    /**
     * @dataProvider ipAddressParams
     */
    public function testAdminDenyHost($ip, $valid)
    {
        $this->formData['admin_deny_hosts'] = $ip;
        $this->form->submit($this->formData);
        $this->assertSame($valid, $this->form['admin_deny_hosts']->isValid());
    }
}
