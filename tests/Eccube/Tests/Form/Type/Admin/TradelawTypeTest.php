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


    public function testInvalidLawTerm01_Blank()
    {
        $this->formData['law_term01'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawTerm02_Blank()
    {
        $this->formData['law_term02'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawTerm03_Blank()
    {
        $this->formData['law_term03'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawTerm04_Blank()
    {
        $this->formData['law_term04'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawTerm05_Blank()
    {
        $this->formData['law_term05'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawTerm06_Blank()
    {
        $this->formData['law_term06'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawCompany_Blank()
    {
        $this->formData['law_company'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawManager_Blank()
    {
        $this->formData['law_manager'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawZip01_Blank()
    {
        $this->formData['law_zip']['law_zip01'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawZip02_Blank()
    {
        $this->formData['law_zip']['law_zip02'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawPref_Blank()
    {
        $this->formData['law_address']['law_pref'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawAddr01_Blank()
    {
        $this->formData['law_address']['law_addr01'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawAddr02_Blank()
    {
        $this->formData['law_address']['law_addr02'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawEmail_Blank()
    {
        $this->formData['law_email'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawUrl_Blank()
    {
        $this->formData['law_url'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawUrl()
    {
        $this->formData['law_url'] = 'hogehoge';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidLawUrl_Nihongo()
    {
        $this->formData['law_url'] = 'http://日本語.com';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }
}
