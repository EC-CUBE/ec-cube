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

use Eccube\Form\Type\Admin\TradelawType;

class TradelawTypeTest extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'company' => '販売業者名',
        'manager' => '運営責任者名',
        'zip' => array(
            'zip01' => '530',
            'zip02' => '0001',
        ),
        'address' => array(
            'pref' => '5',
            'addr01' => '北区',
            'addr02' => '梅田',
        ),
        'tel' => array(
            'tel01' => '03',
            'tel02' => '1111',
            'tel03' => '1111',
        ),
        'fax' => array(
            'fax01' => '03',
            'fax02' => '1111',
            'fax03' => '4444',
        ),
        'email' => 'eccube@example.com',
        'url' => 'http://www.eccube.net',
        'term01' => 'term01',
        'term02' => 'term01',
        'term03' => 'term01',
        'term04' => 'term01',
        'term05' => 'term01',
        'term06' => 'term01',
    );

    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder(TradelawType::class, null, array(
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
        $this->formData['fax']['fax01'] = '';
        $this->formData['fax']['fax02'] = '';
        $this->formData['fax']['fax03'] = '';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidTel_Blank()
    {
        $this->formData['tel']['tel01'] = '';
        $this->formData['tel']['tel02'] = '';
        $this->formData['tel']['tel03'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }


    public function testInvalidLawTerm01_Blank()
    {
        $this->formData['term01'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawTerm02_Blank()
    {
        $this->formData['term02'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawTerm03_Blank()
    {
        $this->formData['term03'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawTerm04_Blank()
    {
        $this->formData['term04'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawTerm05_Blank()
    {
        $this->formData['term05'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawTerm06_Blank()
    {
        $this->formData['term06'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawCompany_Blank()
    {
        $this->formData['company'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawManager_Blank()
    {
        $this->formData['manager'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawZip01_Blank()
    {
        $this->formData['zip']['zip01'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawZip02_Blank()
    {
        $this->formData['zip']['zip02'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawPref_Blank()
    {
        $this->formData['address']['pref'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawAddr01_Blank()
    {
        $this->formData['address']['addr01'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawAddr02_Blank()
    {
        $this->formData['address']['addr02'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawEmail_Blank()
    {
        $this->formData['email'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawUrl_Blank()
    {
        $this->formData['url'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidLawUrl()
    {
        $this->formData['url'] = 'hogehoge';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidLawUrl_Nihongo()
    {
        $this->formData['url'] = 'http://日本語.com';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }
}
