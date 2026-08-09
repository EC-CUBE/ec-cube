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

namespace Eccube\Tests\Form\Type\Admin;

use Eccube\Entity\BaseInfo;
use Eccube\Form\Type\Admin\ShopMasterType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use Symfony\Component\Form\FormInterface;

final class ShopMasterTypeTest extends AbstractTypeTestCase
{
    protected ?FormInterface $form = null;

    /**
     * @var array|null デフォルト値（正常系）を設定
     */
    protected ?array $formData = [
        /*
        'company_name' => '会社名',
        'company_kana' => 'カナ',
         */
        'shop_name' => '店舗名',
        /*
        'shop_kana' => 'カナ',
        'shop_name_eng' => 'shopname',
        'postal_code' => '530-0001',
        'address' => array(
            'pref' => '5',
            'addr01' => '北区',
            'addr02' => '梅田',
        ),
         */
        'phone_number' => '012-345-6789',
        /*
        'business_hour' => '店舗営業時間',
         */
        'email01' => 'eccube@example.com',
        'email02' => 'eccube@example.com',
        'email03' => 'eccube@example.com',
        'email04' => 'eccube@example.com',
        'delivery_free_amount' => '1000',
        'delivery_free_quantity' => '1000',
        /*
        'good_traded' => '取り扱い商品',
        'message' => 'メッセージ',
        'option_product_delivery_fee' => '0',
        'option_delivery_fee' => '0',
        'option_customer_activate' => '0',
        'option_mypage_order_status_display' => '0',
        'option_favorite_product' => 0,
        'option_remember_me' => '0',
        'option_nostock_hidden' => '0',
         */
    ];

    protected function setUp(): void
    {
        parent::setUp();
        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(ShopMasterType::class, null, ['csrf_protection' => false])
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidPhoneNumberBlank()
    {
        $this->formData['phone_number'] = '';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidDeliveryFreeAmountOverMaxLength()
    {
        $this->formData['delivery_free_amount'] = '12345678900';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidDeliveryFreeAmountNotNumeric()
    {
        $this->formData['delivery_free_amount'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidDeliveryFreeAmountHasMinus()
    {
        $this->formData['delivery_free_amount'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidDeliveryFreeQuantityNotNumeric()
    {
        $this->formData['delivery_free_quantity'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidDeliveryFreeQuantityHasMinus()
    {
        $this->formData['delivery_free_quantity'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidBasicPointRateRangeMin()
    {
        $this->formData['basic_point_rate'] = '0';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidBasicPointRateRangeMax()
    {
        $this->formData['basic_point_rate'] = '100';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidBasicPointRateRangeMin()
    {
        $this->formData['basic_point_rate'] = '-1';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidBasicPointRateRangeMax()
    {
        $this->formData['basic_point_rate'] = '101';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidGoodTradedMaxLength()
    {
        $this->formData['good_traded'] = str_repeat('1', $this->eccubeConfig['eccube_ltext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidMessageMaxLength()
    {
        $this->formData['message'] = str_repeat('1', $this->eccubeConfig['eccube_ltext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidOpeningHours(): void
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => ['Monday', 'Tuesday'], 'opens' => '09:00', 'closes' => '18:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidOpeningHoursOpensAfterCloses(): void
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => ['PublicHolidays'], 'opens' => '20:00', 'closes' => '15:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidOpeningHoursMissingDay(): void
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => [], 'opens' => '09:00', 'closes' => '18:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidOpeningHoursOverlapSameDay(): void
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => ['Saturday'], 'opens' => '10:00', 'closes' => '15:00'],
            ['day_of_week' => ['Saturday'], 'opens' => '14:00', 'closes' => '18:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    /**
     * 画面で中間行を削除すると送信キーが歯抜け（0, 2 等）になるため、
     * 重複エラーは詰めた通し番号ではなく実在する子フォームのキーに付く必要がある.
     */
    public function testInValidOpeningHoursOverlapAttachesErrorToSubmittedKey(): void
    {
        $this->formData['OpeningHours'] = [
            0 => ['day_of_week' => ['Saturday'], 'opens' => '10:00', 'closes' => '15:00'],
            2 => ['day_of_week' => ['Saturday'], 'opens' => '14:00', 'closes' => '18:00'],
        ];
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
        $this->assertCount(1, $this->form->get('OpeningHours')->get('2')->get('closes')->getErrors());
    }

    public function testValidOpeningHoursDifferentDayNoOverlap(): void
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => ['Saturday'], 'opens' => '10:00', 'closes' => '15:00'],
            ['day_of_week' => ['Sunday'], 'opens' => '10:00', 'closes' => '15:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidSameAsMultipleUrls(): void
    {
        $this->formData['same_as'] = "https://example.com/a\nhttps://example.com/b";
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidSameAsContainsNonUrl(): void
    {
        $this->formData['same_as'] = "https://example.com/a\nnot-a-url";
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidSameAsMaxLength(): void
    {
        // 形式は有効なURLだが最大長を超えるケース（長さ制約のみを検証）
        $this->formData['same_as'] = 'https://example.com/'.str_repeat('a', $this->eccubeConfig['eccube_ltext_len']);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidNumberOfEmployeesZero(): void
    {
        $this->formData['number_of_employees'] = '0';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidNumberOfEmployeesNegative(): void
    {
        $this->formData['number_of_employees'] = '-1';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidNumberOfEmployeesIntMax(): void
    {
        $this->formData['number_of_employees'] = '2147483647';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidNumberOfEmployeesOverIntMax(): void
    {
        $this->formData['number_of_employees'] = '2147483648';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidCopyrightYearRangeMin(): void
    {
        $this->formData['copyright_year'] = '1900';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidCopyrightYearRangeMax(): void
    {
        $this->formData['copyright_year'] = '9999';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidCopyrightYearBelowMin(): void
    {
        $this->formData['copyright_year'] = '1899';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidCopyrightYearAboveMax(): void
    {
        $this->formData['copyright_year'] = '10000';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidFoundingDatePast(): void
    {
        $this->formData['founding_date'] = '2000-04-01';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidFoundingDateFuture(): void
    {
        $this->formData['founding_date'] = (new \DateTime('+1 year'))->format('Y-m-d');
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidSiteImageUrl(): void
    {
        $this->formData['site_image'] = 'https://example.com/site.png';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidSiteImageNotUrl(): void
    {
        $this->formData['site_image'] = 'not-a-url';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidSiteImageMaxLength(): void
    {
        // 形式は有効なURLだが最大長を超えるケース（長さ制約のみを検証）
        $this->formData['site_image'] = 'https://example.com/'.str_repeat('a', $this->eccubeConfig['eccube_stext_len']);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    /**
     * 納品書PDFの出力項目トグルが BaseInfo にマッピングされること (#6197).
     * チェックボックスは未チェックをキー欠落で表すため, OFF はキーを送らないことで再現する.
     */
    public function testOrderPdfVisibleTogglesMappedToEntity(): void
    {
        $BaseInfo = new BaseInfo();
        $form = $this->formFactory
            ->createBuilder(ShopMasterType::class, $BaseInfo, ['csrf_protection' => false])
            ->getForm();

        $formData = $this->formData;
        // 既定 OFF の店舗営業時間を ON にし, 既定 ON の店名はキーを送らず OFF にする
        $formData['order_pdf_visible_business_hour'] = '1';

        $form->submit($formData);

        $this->assertTrue($form->isValid());
        $this->assertFalse($BaseInfo->isOrderPdfVisibleShopName());
        $this->assertTrue($BaseInfo->isOrderPdfVisibleBusinessHour());
    }
}
