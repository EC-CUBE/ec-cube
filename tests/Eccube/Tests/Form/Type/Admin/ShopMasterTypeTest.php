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

    public function testValidOpeningHours()
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => ['Monday', 'Tuesday'], 'opens' => '09:00', 'closes' => '18:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidOpeningHoursOpensAfterCloses()
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => ['PublicHolidays'], 'opens' => '20:00', 'closes' => '15:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidOpeningHoursMissingDay()
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => [], 'opens' => '09:00', 'closes' => '18:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidOpeningHoursOverlapSameDay()
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => ['Saturday'], 'opens' => '10:00', 'closes' => '15:00'],
            ['day_of_week' => ['Saturday'], 'opens' => '14:00', 'closes' => '18:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidOpeningHoursDifferentDayNoOverlap()
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => ['Saturday'], 'opens' => '10:00', 'closes' => '15:00'],
            ['day_of_week' => ['Sunday'], 'opens' => '10:00', 'closes' => '15:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidSameAsMultipleUrls()
    {
        $this->formData['same_as'] = "https://example.com/a\nhttps://example.com/b";
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidSameAsContainsNonUrl()
    {
        $this->formData['same_as'] = "https://example.com/a\nnot-a-url";
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidSameAsMaxLength()
    {
        // 形式は有効なURLだが最大長を超えるケース（長さ制約のみを検証）
        $this->formData['same_as'] = 'https://example.com/'.str_repeat('a', $this->eccubeConfig['eccube_ltext_len']);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidNumberOfEmployeesZero()
    {
        $this->formData['number_of_employees'] = '0';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidNumberOfEmployeesNegative()
    {
        $this->formData['number_of_employees'] = '-1';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidCopyrightYearRangeMin()
    {
        $this->formData['copyright_year'] = '1900';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidCopyrightYearRangeMax()
    {
        $this->formData['copyright_year'] = '9999';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidCopyrightYearBelowMin()
    {
        $this->formData['copyright_year'] = '1899';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidCopyrightYearAboveMax()
    {
        $this->formData['copyright_year'] = '10000';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidFoundingDatePast()
    {
        $this->formData['founding_date'] = '2000-04-01';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidFoundingDateFuture()
    {
        $this->formData['founding_date'] = (new \DateTime('+1 year'))->format('Y-m-d');
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidSiteImageUrl()
    {
        $this->formData['site_image'] = 'https://example.com/site.png';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidSiteImageNotUrl()
    {
        $this->formData['site_image'] = 'not-a-url';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidSiteImageMaxLength()
    {
        // 形式は有効なURLだが最大長を超えるケース（長さ制約のみを検証）
        $this->formData['site_image'] = 'https://example.com/'.str_repeat('a', $this->eccubeConfig['eccube_stext_len']);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
