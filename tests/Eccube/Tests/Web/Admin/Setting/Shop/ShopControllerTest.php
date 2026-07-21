<?php

declare(strict_types=1);

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

namespace Eccube\Tests\Web\Admin\Setting\Shop;

use Eccube\Entity\BaseInfo;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ShopControllerTest
 */
#[Group('cache-clear')]
final class ShopControllerTest extends AbstractAdminWebTestCase
{
    /**
     * Routing
     */
    public function testRouting()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_setting_shop'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @param bool $isSuccess
     * @param bool $expected
     */
    #[DataProvider(methodName: 'dataSubmitProvider')]
    #[Group(name: 'cache-clear')]
    public function testSubmit($isSuccess, $expected)
    {
        $formData = $this->createFormData();
        if (!$isSuccess) {
            $formData['shop_name'] = '';
        }
        $this->client->request(
            Request::METHOD_POST,
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

        return [
            '_token' => 'dummy',
            'company_name' => '会社名',
            'company_kana' => 'カナ',
            'shop_name' => '店舗名',
            'shop_kana' => 'カナ',
            'shop_name_eng' => 'shopname',
            'postal_code' => '060-0000',
            'address' => [
                'pref' => '5',
                'addr01' => '北区',
                'addr02' => '梅田',
            ],
            'phone_number' => '012-345-6789',
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
            'option_sanitize_csv_formulas' => '0',
        ];
    }

    /**
     * CSVの数式インジェクション対策トグルが BaseInfo に保存されること.
     * チェックボックスは未チェックをキー欠落で表すため, 無効化はキーを送らないことで再現する.
     */
    #[DataProvider(methodName: 'dataSanitizeCsvFormulasProvider')]
    #[Group(name: 'cache-clear')]
    public function testSubmitPersistsSanitizeCsvFormulasOption(bool $checked, bool $expected): void
    {
        $formData = $this->createFormData();
        if ($checked) {
            $formData['option_sanitize_csv_formulas'] = '1';
        } else {
            unset($formData['option_sanitize_csv_formulas']);
        }
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_setting_shop'),
            ['shop_master' => $formData]
        );

        $this->entityManager->clear();
        $BaseInfo = $this->entityManager->getRepository(BaseInfo::class)->find(1);
        $this->assertInstanceOf(BaseInfo::class, $BaseInfo);
        $this->assertSame($expected, $BaseInfo->isOptionSanitizeCsvFormulas());
    }

    public static function dataSanitizeCsvFormulasProvider(): \Iterator
    {
        yield [true, true];
        yield [false, false];
    }

    /**
     * 納品書PDFの出力項目トグルが BaseInfo に保存されること (#6197).
     * 既定 ON の項目を OFF に、既定 OFF の項目を ON にできることの双方を確認する.
     * チェックボックスは未チェックをキー欠落で表すため, 無効化はキーを送らないことで再現する.
     */
    #[DataProvider(methodName: 'dataOrderPdfVisibleProvider')]
    #[Group(name: 'cache-clear')]
    public function testSubmitPersistsOrderPdfVisibleOptions(bool $checked, bool $expected): void
    {
        $formData = $this->createFormData();
        foreach (['order_pdf_visible_shop_name', 'order_pdf_visible_message'] as $key) {
            if ($checked) {
                $formData[$key] = '1';
            } else {
                unset($formData[$key]);
            }
        }
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_setting_shop'),
            ['shop_master' => $formData]
        );

        $this->entityManager->clear();
        $BaseInfo = $this->entityManager->getRepository(BaseInfo::class)->find(1);
        $this->assertInstanceOf(BaseInfo::class, $BaseInfo);
        // 既定 ON の店名, 既定 OFF のメッセージ, いずれも送信内容どおりに保存される
        $this->assertSame($expected, $BaseInfo->isOrderPdfVisibleShopName());
        $this->assertSame($expected, $BaseInfo->isOrderPdfVisibleMessage());
    }

    public static function dataOrderPdfVisibleProvider(): \Iterator
    {
        yield [true, true];
        yield [false, false];
    }

    public static function dataSubmitProvider(): \Iterator
    {
        yield [false, false];
        yield [true, true];
    }

    /**
     * testMailNoRFC
     */
    public function testMailNoRFC()
    {
        $formData = $this->createFormData();
        // RFCに準拠していないメールアドレスを設定
        $formData['email01'] = 'aa..@example.com';
        $formData['email02'] = 'aa..@example.com';
        $formData['email03'] = 'aa..@example.com';
        $formData['email04'] = 'aa..@example.com';
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_setting_shop'),
            ['shop_master' => $formData]
        );
        $BaseInfo = $this->entityManager->getRepository(BaseInfo::class)->find(1);
        $this->assertInstanceOf(BaseInfo::class, $BaseInfo);

        $this->expected = $BaseInfo->getEmail01();
        $this->actual = $formData['email01'];
        $this->verify();
    }
}
