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

namespace Eccube\Tests\Form\Type\Admin;

use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class DeliveryFeeTypeTest extends AbstractTypeTestCase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /**
     * getValidTestData
     *
     * 正常系のデータパターンを返す
     *
     * @access public
     * @return array
     */
    public function getValidTestData()
    {
        return array(
            array(
                'data' => array(
                    'fee' => 0,
                ),
            ),
            array(
                'data' => array(
                    'fee' => 1,
                ),
            ),
            array(
                'data' => array(
                    'fee' => '0',
                ),
            ),
            array(
                'data' => array(
                    'fee' => '1',
                ),
            ),
        );
    }

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('delivery_fee', null, array(
                'csrf_protection' => false,
            ))
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
        $this->form->submit(array('fee' => str_repeat('1', $this->app['config']['price_len'])));
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidData_Blank()
    {
        $this->form->submit(array('fee' => ''));
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidData_Minus()
    {
        $this->form->submit(array('fee' => '-1'));
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidData_PriceLen()
    {
        $this->form->submit(array('fee' => str_repeat('1', $this->app['config']['price_len']+1)));
        $this->assertFalse($this->form->isValid());
    }
}
