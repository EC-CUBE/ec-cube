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

class TradelawTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'law_company' => '販売業者名',
        'law_manager' => '運営責任者名',
        'law_zip' => array(
            'law_zip01' => '530',
            'law_zip02' => '0001',
        ),
        'law_address' => array(
            'law_pref' => '5',
            'law_addr01' => '北区',
            'law_addr02' => '梅田',
        ),
        'law_tel' => array(
            'law_tel01' => '03',
            'law_tel02' => '1111',
            'law_tel03' => '1111',
        ),
        'law_fax' => array(
            'law_fax01' => '03',
            'law_fax02' => '1111',
            'law_fax03' => '4444',
        ),
        'law_email' => 'eccube@example.com',
        'law_url' => 'http://www.eccube.net',
        'law_term01' => 'law_term01',
        'law_term02' => 'law_term01',
        'law_term03' => 'law_term01',
        'law_term04' => 'law_term01',
        'law_term05' => 'law_term01',
        'law_term06' => 'law_term01',
        'law_term07' => 'law_term01',
        'law_term08' => 'law_term01',
        'law_term09' => 'law_term01',
        'law_term10' => 'law_term01',
    );

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('tradelaw', null, array(
                'csrf_protection' => false,
            ))
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
        // エラーメッセージデバッグ用
        //var_dump($this->form->getErrorsAsString());die;
    }

    public function testValidFax_Blank()
    {
        $this->formData['law_fax']['law_fax01'] = '';
        $this->formData['law_fax']['law_fax02'] = '';
        $this->formData['law_fax']['law_fax03'] = '';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidTel_Blank()
    {
        $this->formData['law_tel']['law_tel01'] = '';
        $this->formData['law_tel']['law_tel02'] = '';
        $this->formData['law_tel']['law_tel03'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
