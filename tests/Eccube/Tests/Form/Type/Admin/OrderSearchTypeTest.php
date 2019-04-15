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

class OrderSearchTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('admin_search_order', null, array(
                'csrf_protection' => false,
            ))
            ->getForm();
    }

    public function testTel_ValidData()
    {
        $formData = array(
            'tel' => '012345'
        );

        $this->form->submit($formData);
        $this->assertTrue($this->form->isValid());
    }
    
    public function testTel_NotValidData()
    {
        //意味あんだか良くわからんが一応書いとく
        $formData = array(
            'tel' => '+〇三=abcふれ'
        );

        $this->form->submit($formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testKana_NotValidData()
    {
        $formData = array(
            'kana' => 'a'
        );

        $this->form->submit($formData);
        $this->assertFalse($this->form->isValid());
    }
}
