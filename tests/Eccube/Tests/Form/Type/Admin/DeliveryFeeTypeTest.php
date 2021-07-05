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

use Eccube\Form\Type\Admin\DeliveryFeeType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class DeliveryFeeTypeTest extends AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /**
     * getValidTestData
     *
     * 正常系のデータパターンを返す
     *
     * @return array
     */
    public function getValidTestData()
    {
        return [
            [
                'data' => [
                    'fee' => 0,
                ],
            ],
            [
                'data' => [
                    'fee' => 1,
                ],
            ],
            [
                'data' => [
                    'fee' => '0',
                ],
            ],
            [
                'data' => [
                    'fee' => '1',
                ],
            ],
        ];
    }

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(DeliveryFeeType::class, null, [
                'csrf_protection' => false,
            ])
            ->getForm();
    }

    /**
     * @dataProvider getValidTestData
     */
    public function testValidData($data)
    {
        $this->form->submit($data);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidDataPriceLen()
    {
        $this->form->submit(['fee' => str_repeat('1', $this->eccubeConfig['eccube_price_len'])]);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidDataBlank()
    {
        $this->form->submit(['fee' => '']);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDataMinus()
    {
        $this->form->submit(['fee' => '-1']);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidDataPriceLen()
    {
        $this->form->submit(['fee' => $this->eccubeConfig['eccube_price_max'] + 1]);
        $this->assertFalse($this->form->isValid());
    }
}
