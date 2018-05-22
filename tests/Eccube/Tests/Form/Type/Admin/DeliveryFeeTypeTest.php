<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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

    public function testValidData_PriceLen()
    {
        $this->form->submit(['fee' => str_repeat('1', $this->eccubeConfig['eccube_price_len'])]);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidData_Blank()
    {
        $this->form->submit(['fee' => '']);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidData_Minus()
    {
        $this->form->submit(['fee' => '-1']);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidData_PriceLen()
    {
        $this->form->submit(['fee' => $this->eccubeConfig['eccube_price_max'] + 1]);
        $this->assertFalse($this->form->isValid());
    }
}
