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

namespace Eccube\Tests\Form\Type\Front;

use Eccube\Form\Type\Front\ForgotType;

class ForgotTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    protected $formData;

    /**
     * 異常系のデータパターンを返す
     *
     * @return array
     */
    public function getInvalidTestData()
    {
        return [
            [
                'data' => [
                    'login_email' => '',
                ],
            ],
            [
                'data' => [
                    'login_email' => 'example',
                ],
            ],
            [
                'data' => [
                    'login_email' => 'a..a@aa',
                ],
            ],
            [
                'data' => [
                    'login_email' => 'aa.@aa',
                ],
            ],
            [
                'data' => [
                    'login_email' => 'aa@adf@a.com',
                ],
            ],
        ];
    }

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(ForgotType::class, null, [
                'csrf_protection' => false,
            ])
            ->getForm();
    }

    /**
     * @dataProvider getInvalidTestData
     */
    public function testInvalidData($data)
    {
        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_Blank()
    {
        $this->formData['login_email'] = 'example@example.com';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }
}
