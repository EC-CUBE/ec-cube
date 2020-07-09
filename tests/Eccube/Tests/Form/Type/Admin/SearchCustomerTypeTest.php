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

use Eccube\Form\Type\Admin\SearchCustomerType;
use Symfony\Component\Form\FormInterface;

class SearchCustomerTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(SearchCustomerType::class, null, ['csrf_protection' => false])
            ->getForm();
    }

    public function testPhoneNumber_NotValidData()
    {
        $formData = [
            'phone_number' => str_repeat('A', 55),
        ];

        $this->form->submit($formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testBuyProductName_NotValiedData()
    {
        $formData = [
            'buy_product_name' => str_repeat('A', $this->eccubeConfig['eccube_stext_len'] + 1),
        ];

        $this->form->submit($formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testBuyProductCode_NotValiedData()
    {
        $formData = [
            'buy_product_code' => str_repeat('A', $this->eccubeConfig['eccube_stext_len'] + 1),
        ];

        $this->form->submit($formData);
        $this->assertFalse($this->form->isValid());
    }

    /**
     * EC-CUBE 4.0.4 以前のバージョンで互換性を保つため yyyy-MM-dd のフォーマットもチェック
     *
     * @dataProvider dataFormDateProvider
     *
     * @param string $formName
     * @param string $formValue
     * @param bool $result
     */
    public function testDateSearch(string $formName, string $formValue, bool $result)
    {
        $formData = [
            $formName => $formValue,
        ];

        $this->form->submit($formData);
        $this->assertEquals($result, $this->form->isValid());
    }

    /**
     * Data provider date form test.
     *
     * @return array
     */
    public function dataFormDateProvider()
    {
        return [
            ['create_date_start', '2020-07-09', true],
            ['create_date_start', '2020-07-09 09:00', true],
            ['create_date_start', '2020-07-09 aa', false],
            ['update_date_start', '2020-07-09', true],
            ['update_date_start', '2020-07-09 09:00', true],
            ['update_date_start', '2020-07-09 aa', false],
            ['last_buy_start', '2020-07-09', true],
            ['last_buy_start', '2020-07-09 09:00', true],
            ['last_buy_start', '2020-07-09 aa', false],
            ['create_date_end', '2020-07-09', true],
            ['create_date_end', '2020-07-09 09:00', true],
            ['create_date_end', '2020-07-09 aa', false],
            ['update_date_end', '2020-07-09', true],
            ['update_date_end', '2020-07-09 09:00', true],
            ['update_date_end', '2020-07-09 aa', false],
            ['last_buy_end', '2020-07-09', true],
            ['last_buy_end', '2020-07-09 09:00', true],
            ['last_buy_end', '2020-07-09 aa', false],
            ['birth_start', '2020-07-09', true],
            ['birth_end', '2020-07-09', true],
            ['phone_number', '2020-07-09', true],
        ];
    }
}
