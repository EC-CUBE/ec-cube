<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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

class ForgotTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /**
     * 異常系のデータパターンを返す
     *
     * @access public
     * @return array
     */
    public function getInvalidTestData()
    {
        return array(
            array(
                'data' => array(
                    'login_email' => ''
                ),
            ),
            array(
                'data' => array(
                    'login_email' => 'example'
                ),
            ),
            array(
                'data' => array(
                    'login_email' => 'a..a@aa'
                ),
            ),
            array(
                'data' => array(
                    'login_email' => 'aa.@aa'
                ),
            ),
            array(
                'data' => array(
                    'login_email' => 'aa@adf@a.com'
                ),
            ),
        );
    }

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('forgot', null, array(
                'csrf_protection' => false,
            ))
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
