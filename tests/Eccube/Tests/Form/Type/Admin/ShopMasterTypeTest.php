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
        // 既定 OFF の店舗営業時間・メッセージを ON にし, 既定 ON の店名はキーを送らず OFF にする
        $formData['order_pdf_visible_business_hour'] = '1';
        $formData['order_pdf_visible_message'] = '1';

        $form->submit($formData);

        $this->assertTrue($form->isValid());
        $this->assertFalse($BaseInfo->isOrderPdfVisibleShopName());
        $this->assertTrue($BaseInfo->isOrderPdfVisibleBusinessHour());
        $this->assertTrue($BaseInfo->isOrderPdfVisibleMessage());
    }
}
