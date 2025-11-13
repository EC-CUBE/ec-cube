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
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Form\FormInterface;

class SecurityTypeTest extends AbstractTypeTestCase
{
    protected ?FormInterface $form = null;

    /**
     * @var array|null デフォルト値（正常系）を設定
     */
    protected ?array $formData = [
        'admin_route_dir' => 'admin',
        'admin_allow_hosts' => '',
        'admin_deny_hosts' => '',
        'front_allow_hosts' => '',
        'front_deny_hosts' => '',
        'trusted_hosts' => 'localhost',
    ];

    #[\Override]
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
     * @param mixed $rootDir
     * @param mixed $valid
     */
    #[DataProvider(methodName: 'adminRouteDirParams')]
    public function testAdminRouteDir($rootDir, $valid)
    {
        $this->formData['admin_route_dir'] = $rootDir;
        $this->form->submit($this->formData);
        $this->assertEquals($valid, $this->form->isValid());
    }

    public static function adminRouteDirParams(): \Iterator
    {
        yield ['admin', true];
        yield ['ADMIN', true];
        yield ['12345', true];
        yield ['adminADMIN123', true];
        yield ['admin_admin', true];
        yield ['/admin', false];
        yield ['admin/', false];
        yield ['admin/route', false];
        yield ['admin&', false];
        yield ['admin?', false];
        yield ['/admin/content/news/page/{page_no}', false];
        yield ['/admin/disable_maintenance/{mode}', false];
        yield ['/admin/content/news/page/{page_no}', false];
        yield ['/admin/product/class_category/{class_name_id}/{id}/edit', false];
        yield ['cart_admin', true];
        yield ['admin_cart', true];
        yield ['product_admin', true];
        yield ['admin_products', true];
        yield ['cart', false];
        yield ['cart&', false];
        yield ['cart?', false];
        yield ['/cart', false];
        yield ['/cart/', false];
        yield ['/cart/buystep', false];
        yield ['/cart/buystep&', false];
        yield ['/cart/buystep?', false];
        yield ['/cart/buystep/', false];
        yield ['/cart/buystep/{cart_key}', false];
        yield ['/cart/{operation}/{productClassId}', false];
        yield ['contact', false];
        yield ['contact&', false];
        yield ['contact?', false];
        yield ['/contact', false];
        yield ['/contact/', false];
        yield ['/contact/complete', false];
        yield ['/contact/complete&', false];
        yield ['/contact/complete?', false];
        yield ['entry', false];
        yield ['entry?', false];
        yield ['entry&', false];
        yield ['/entry', false];
        yield ['/entry&', false];
        yield ['/entry?', false];
        yield ['/entry/', false];
        yield ['/entry/complete', false];
        yield ['/entry/complete&', false];
        yield ['/entry/complete?', false];
        yield ['/entry/activate', false];
        yield ['/entry/activate?', false];
        yield ['/entry/activate&', false];
        yield ['/entry/activate/', false];
        yield ['/entry/activate/{secret_key}/{qtyInCart}', false];
        yield ['/forgot', false];
        yield ['/forgot&', false];
        yield ['/forgot?', false];
        yield ['/forgot/complete', false];
        yield ['/forgot/complete?', false];
        yield ['/forgot/complete&', false];
        yield ['/forgot/reset', false];
        yield ['/forgot/reset&', false];
        yield ['/forgot/reset?', false];
        yield ['/forgot/reset/', false];
        yield ['/forgot/reset/{reset_key}', false];
        yield ['/help/tradelaw', false];
        yield ['/help/tradelaw&', false];
        yield ['/help/tradelaw?', false];
        yield ['/guide', false];
        yield ['/guide&', false];
        yield ['/guide?', false];
        yield ['/help/about', false];
        yield ['/help/about&', false];
        yield ['/help/about?', false];
        yield ['/help/privacy', false];
        yield ['/help/privacy&', false];
        yield ['/help/privacy?', false];
        yield ['/help/agreement', false];
        yield ['/help/agreement&', false];
        yield ['/help/agreement?', false];
        yield ['/install/plugins', false];
        yield ['/install/plugins&', false];
        yield ['/install/plugins?', false];
        yield ['/install/plugin', false];
        yield ['/install/plugin&', false];
        yield ['/install/plugin?', false];
        yield ['/install/plugin/', false];
        yield ['/install/plugin/redirect', false];
        yield ['/install/plugin/redirect?', false];
        yield ['/install/plugin/redirect&', false];
        yield ['/install/plugin/{code}/enable', false];
        yield ['/install', false];
        yield ['/install?', false];
        yield ['/install&', false];
        yield ['/install/', false];
        yield ['/install/step1', false];
        yield ['/install/step1?', false];
        yield ['/install/step1&', false];
        yield ['/install/step1/', false];
        yield ['/install/step2', false];
        yield ['/install/step2?', false];
        yield ['/install/step2&', false];
        yield ['/install/step2/', false];
        yield ['/install/step3', false];
        yield ['/install/step3?', false];
        yield ['/install/step3&', false];
        yield ['/install/step3/', false];
        yield ['/install/step4', false];
        yield ['/install/step4?', false];
        yield ['/install/step4&', false];
        yield ['/install/step4/', false];
        yield ['/install/step5', false];
        yield ['/install/step5?', false];
        yield ['/install/step5&', false];
        yield ['/install/step5/', false];
        yield ['/install/complete', false];
        yield ['/install/complete?', false];
        yield ['/install/complete&', false];
        yield ['/mypage/change', false];
        yield ['/mypage/change?', false];
        yield ['/mypage/change&', false];
        yield ['/mypage/change/', false];
        yield ['/mypage/change_complete', false];
        yield ['/mypage/change_complete?', false];
        yield ['/mypage/change_complete&', false];
        yield ['/mypage/change_complete/', false];
        yield ['/mypage/delivery', false];
        yield ['/mypage/delivery?', false];
        yield ['/mypage/delivery&', false];
        yield ['/mypage/delivery/', false];
        yield ['/mypage/delivery/new', false];
        yield ['/mypage/delivery/new?', false];
        yield ['/mypage/delivery/new&', false];
        yield ['/mypage/delivery/new/', false];
        yield ['/mypage/delivery/{id}/edit', false];
        yield ['/mypage/login', false];
        yield ['/mypage/login?', false];
        yield ['/mypage/login&', false];
        yield ['/mypage/login/', false];
        yield ['/mypage/', false];
        yield ['/mypage/history', false];
        yield ['/mypage/history?', false];
        yield ['/mypage/history&', false];
        yield ['/mypage/history/', false];
        yield ['/mypage/order', false];
        yield ['/mypage/order?', false];
        yield ['/mypage/order&', false];
        yield ['/mypage/order/', false];
        yield ['/mypage/order/{order_no}', false];
        yield ['/mypage/favorite', false];
        yield ['/mypage/withdraw', false];
        yield ['/mypage/withdraw', false];
        yield ['/mypage/withdraw_complete', false];
        yield ['/shopping/nonmember', false];
        yield ['/shopping/customer', false];
        yield ['products', false];
        yield ['products?', false];
        yield ['products&', false];
        yield ['/products', false];
        yield ['/products/list', false];
        yield ['/products/detail/{id}', false];
        yield ['/products/add_favorite/{id}', false];
        yield ['/products/add_cart/{id}', false];
        yield ['/shopping/shipping_multiple', false];
        yield ['/shopping/shipping_multiple_edit', false];
        yield ['/shopping/shipping/{id}', false];
        yield ['/shopping', false];
        yield ['/shopping/redirect_to', false];
        yield ['/shopping/confirm', false];
        yield ['/shopping/checkout', false];
        yield ['/shopping/complete', false];
        yield ['/shopping/login', false];
        yield ['/shopping/error', false];
        yield ['/', false];
        yield ['/logout', false];
        yield ['/sitemap.xml', false];
        yield ['/sitemap_category.xml', false];
        yield ['/sitemap_product_{page}.xml', false];
        yield ['/sitemap_page.xml', false];
        yield ['/user_data/{route}', false];
    }

    public function testTrustedHosts()
    {
        $this->formData['trusted_hosts'] = '^127\.0\.0.1$,^localhost$';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public static function ipAddressParams(): \Iterator
    {
        // 正常系（適切なIPアドレス表記として認める）
        yield ['', true];
        // 空パターン
        yield ['127.0.0.1', true];
        // IPアドレスのみ
        yield ['192.168.56.1/0', true];
        // IPアドレスとビットマスク最小値
        yield ['192.168.56.1/32', true];
        // IPアドレスとビットマスク最大値
        yield ["127.0.0.1\n192.168.56.1/32", true];
        // 複数行に渡る記述
        yield [str_repeat("127.0.0.1\n", 300), true];
        // 300回リピート（3000byte以内チェック）
        // 異常系（IPアドレス表記として認めないパターン）
        yield ['a', false];
        // 表記に従わない記述
        yield ['192.168.56.1/33', false];
        // ビットマスク最大値を超えた値
        yield ['192.168.56.1/a', false];
        // ビットマスクが不正な値
        yield ["127.0.0.1\n192.168.56.1/33", false];
        // 複数行に渡る記述で2行目が不正な値
        yield ['999.168.56.1/32', false];
        // IPアドレスの範囲外
        yield [str_repeat("127.0.0.1\n", 301), false];
    }

    /**
     * @param mixed $ip
     * @param mixed $valid
     */
    #[DataProvider(methodName: 'ipAddressParams')]
    public function testFrontAllowHost($ip, $valid)
    {
        $this->formData['front_allow_hosts'] = $ip;
        $this->form->submit($this->formData);
        $this->assertSame($valid, $this->form['front_allow_hosts']->isValid());
    }

    /**
     * @param mixed $ip
     * @param mixed $valid
     */
    #[DataProvider(methodName: 'ipAddressParams')]
    public function testFrontDenyHost($ip, $valid)
    {
        $this->formData['front_deny_hosts'] = $ip;
        $this->form->submit($this->formData);
        $this->assertSame($valid, $this->form['front_deny_hosts']->isValid());
    }

    /**
     * @param mixed $ip
     * @param mixed $valid
     */
    #[DataProvider(methodName: 'ipAddressParams')]
    public function testAdminAllowHost($ip, $valid)
    {
        $this->formData['admin_allow_hosts'] = $ip;
        $this->form->submit($this->formData);
        $this->assertSame($valid, $this->form['admin_allow_hosts']->isValid());
    }

    /**
     * @param mixed $ip
     * @param mixed $valid
     */
    #[DataProvider(methodName: 'ipAddressParams')]
    public function testAdminDenyHost($ip, $valid)
    {
        $this->formData['admin_deny_hosts'] = $ip;
        $this->form->submit($this->formData);
        $this->assertSame($valid, $this->form['admin_deny_hosts']->isValid());
    }
}
