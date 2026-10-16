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
            // 営業時間の欄が描画されたことを示すセンチネル（shop_master.twig が出力する）
            'opening_hours_rendered' => '1',
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
     * MCP サーバ有効化トグルが BaseInfo に保存されること.
     * チェックボックスは未チェックをキー欠落で表すため, 無効化はキーを送らないことで再現する.
     */
    #[DataProvider(methodName: 'dataMcpEnabledProvider')]
    #[Group(name: 'cache-clear')]
    public function testSubmitPersistsMcpEnabledOption(bool $checked, bool $expected): void
    {
        $formData = $this->createFormData();
        if ($checked) {
            $formData['mcp_enabled'] = '1';
        } else {
            unset($formData['mcp_enabled']);
        }
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_setting_shop'),
            ['shop_master' => $formData]
        );

        $this->entityManager->clear();
        $BaseInfo = $this->entityManager->getRepository(BaseInfo::class)->find(1);
        $this->assertInstanceOf(BaseInfo::class, $BaseInfo);
        $this->assertSame($expected, $BaseInfo->isMcpEnabled());
    }

    public static function dataMcpEnabledProvider(): \Iterator
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
        foreach (['order_pdf_visible_shop_name', 'order_pdf_visible_business_hour'] as $key) {
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
        // 既定 ON の店名, 既定 OFF の店舗営業時間, いずれも送信内容どおりに保存される
        $this->assertSame($expected, $BaseInfo->isOrderPdfVisibleShopName());
        $this->assertSame($expected, $BaseInfo->isOrderPdfVisibleBusinessHour());
    }

    public static function dataOrderPdfVisibleProvider(): \Iterator
    {
        yield [true, true];
        yield [false, false];
    }

    /**
     * 営業時間は画面の行の順に保存され、再表示でも同じ順に並ぶ.
     * 中間行の削除で送信キーが歯抜けになっても、順序はキーではなく行の並びで決まる.
     */
    #[Group(name: 'cache-clear')]
    public function testSubmitPersistsOpeningHoursInScreenOrder(): void
    {
        $formData = $this->createFormData();
        $formData['OpeningHours'] = [
            0 => ['day_of_week' => ['Wednesday'], 'opens' => '09:00', 'closes' => '18:00'],
            2 => ['day_of_week' => ['Monday'], 'opens' => '09:00', 'closes' => '18:00'],
            5 => ['day_of_week' => ['Friday'], 'opens' => '10:00', 'closes' => '19:00'],
        ];
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_setting_shop'),
            ['shop_master' => $formData]
        );
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $this->entityManager->clear();
        $BaseInfo = $this->entityManager->getRepository(BaseInfo::class)->find(1);
        $this->assertInstanceOf(BaseInfo::class, $BaseInfo);
        $actual = [];
        foreach ($BaseInfo->getOpeningHours() as $OpeningHours) {
            $actual[] = [$OpeningHours->getDayOfWeek(), $OpeningHours->getSortNo(), $OpeningHours->getCloses()?->format('H:i')];
        }
        $this->assertSame([
            [['Wednesday'], 0, '18:00'],
            [['Monday'], 1, '18:00'],
            [['Friday'], 2, '19:00'],
        ], $actual);

        // 再表示でも同じ順に並ぶ
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_setting_shop'));
        $this->assertSame('Wednesday', $crawler->filter('#shop_master_OpeningHours_0_day_of_week input:checked')->attr('value'));
        $this->assertSame('Monday', $crawler->filter('#shop_master_OpeningHours_1_day_of_week input:checked')->attr('value'));
        $this->assertSame('Friday', $crawler->filter('#shop_master_OpeningHours_2_day_of_week input:checked')->attr('value'));

        // 既存の 1 行目だけを更新しても順序は変わらない（UPDATE で行の物理的な位置が変わっても sort_no で並ぶ）
        $formData['OpeningHours'] = [
            0 => ['day_of_week' => ['Wednesday'], 'opens' => '09:00', 'closes' => '17:00'],
            1 => ['day_of_week' => ['Monday'], 'opens' => '09:00', 'closes' => '18:00'],
            2 => ['day_of_week' => ['Friday'], 'opens' => '10:00', 'closes' => '19:00'],
        ];
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_setting_shop'),
            ['shop_master' => $formData]
        );
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $this->entityManager->clear();
        $BaseInfo = $this->entityManager->getRepository(BaseInfo::class)->find(1);
        $this->assertInstanceOf(BaseInfo::class, $BaseInfo);
        $actual = [];
        foreach ($BaseInfo->getOpeningHours() as $OpeningHours) {
            $actual[] = [$OpeningHours->getDayOfWeek(), $OpeningHours->getSortNo(), $OpeningHours->getCloses()?->format('H:i')];
        }
        $this->assertSame([
            [['Wednesday'], 0, '17:00'],
            [['Monday'], 1, '18:00'],
            [['Friday'], 2, '19:00'],
        ], $actual);
    }

    /**
     * 既存の営業時間の中間の行を削除して保存すると（送信キーは 0, 2）、残りの行が画面の順に保存される.
     * 送信キーは 0 から詰め直されるため、既存のエンティティへ値が割り当て直されても内容と順序は変わらない.
     */
    #[Group(name: 'cache-clear')]
    public function testSubmitRemovesMiddleOpeningHoursRow(): void
    {
        $formData = $this->createFormData();
        $formData['OpeningHours'] = [
            0 => ['day_of_week' => ['Wednesday'], 'opens' => '09:00', 'closes' => '18:00'],
            1 => ['day_of_week' => ['Monday'], 'opens' => '09:00', 'closes' => '18:00'],
            2 => ['day_of_week' => ['Friday'], 'opens' => '10:00', 'closes' => '19:00'],
        ];
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_setting_shop'),
            ['shop_master' => $formData]
        );
        $this->assertTrue($this->client->getResponse()->isRedirection());

        // 2 行目を削除して保存する
        unset($formData['OpeningHours'][1]);
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_setting_shop'),
            ['shop_master' => $formData]
        );
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $this->entityManager->clear();
        $BaseInfo = $this->entityManager->getRepository(BaseInfo::class)->find(1);
        $this->assertInstanceOf(BaseInfo::class, $BaseInfo);
        $actual = [];
        foreach ($BaseInfo->getOpeningHours() as $OpeningHours) {
            $actual[] = [$OpeningHours->getDayOfWeek(), $OpeningHours->getSortNo(), $OpeningHours->getOpens()?->format('H:i'), $OpeningHours->getCloses()?->format('H:i')];
        }
        $this->assertSame([
            [['Wednesday'], 0, '09:00', '18:00'],
            [['Friday'], 1, '10:00', '19:00'],
        ], $actual);
    }

    /**
     * 営業時間の欄を描画していない画面（shop_master.twig の上書き等）から保存しても、既存の営業時間は残る.
     * センチネル（opening_hours_rendered）も OpeningHours のキーも送られない送信で再現する.
     */
    #[Group(name: 'cache-clear')]
    public function testSubmitKeepsOpeningHoursWhenFieldNotRendered(): void
    {
        // 標準のテンプレートは営業時間の欄と一緒にセンチネルを出力する
        $crawler = $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_setting_shop'));
        $this->assertSame('1', $crawler->filter('#opening-hours-group ~ input[name="shop_master[opening_hours_rendered]"]')->attr('value'));

        $formData = $this->createFormData();
        $formData['OpeningHours'] = [
            0 => ['day_of_week' => ['Monday'], 'opens' => '09:00', 'closes' => '18:00'],
        ];
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_setting_shop'),
            ['shop_master' => $formData]
        );
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $formData = $this->createFormData();
        unset($formData['opening_hours_rendered']);
        $formData['shop_name'] = '営業時間の欄が無い画面から保存';
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_setting_shop'),
            ['shop_master' => $formData]
        );
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $this->entityManager->clear();
        $BaseInfo = $this->entityManager->getRepository(BaseInfo::class)->find(1);
        $this->assertInstanceOf(BaseInfo::class, $BaseInfo);
        // 他の項目は保存され、営業時間は残る
        $this->assertSame('営業時間の欄が無い画面から保存', $BaseInfo->getShopName());
        $this->assertCount(1, $BaseInfo->getOpeningHours());
        $this->assertSame(['Monday'], $BaseInfo->getOpeningHours()->first()->getDayOfWeek());
    }

    /**
     * 既存の営業時間の行の値をすべて消して保存すると、その行は削除される.
     */
    #[Group(name: 'cache-clear')]
    public function testSubmitRemovesClearedOpeningHours(): void
    {
        $formData = $this->createFormData();
        $formData['OpeningHours'] = [
            0 => ['day_of_week' => ['Monday'], 'opens' => '09:00', 'closes' => '18:00'],
            1 => ['day_of_week' => ['Tuesday'], 'opens' => '09:00', 'closes' => '18:00'],
        ];
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_setting_shop'),
            ['shop_master' => $formData]
        );
        $this->assertTrue($this->client->getResponse()->isRedirection());

        // 1 行目の値をすべて消して保存する（曜日のチェックボックスは未チェックだとキーごと送られない）
        $formData['OpeningHours'] = [
            0 => ['opens' => '', 'closes' => ''],
            1 => ['day_of_week' => ['Tuesday'], 'opens' => '09:00', 'closes' => '18:00'],
        ];
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_setting_shop'),
            ['shop_master' => $formData]
        );
        $this->assertTrue($this->client->getResponse()->isRedirection());

        $this->entityManager->clear();
        $BaseInfo = $this->entityManager->getRepository(BaseInfo::class)->find(1);
        $this->assertInstanceOf(BaseInfo::class, $BaseInfo);
        $this->assertCount(1, $BaseInfo->getOpeningHours());
        $this->assertSame(['Tuesday'], $BaseInfo->getOpeningHours()->first()->getDayOfWeek());
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
