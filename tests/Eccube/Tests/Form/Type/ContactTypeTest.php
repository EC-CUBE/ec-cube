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


namespace Eccube\Tests\Form\Type;

class ContactTypeTest extends AbstractTypeTestCase
{

    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'name' => array(
            'name01' => 'たかはし',
            'name02' => 'しんいち',
        ),
        'kana' => array(
            'kana01' => 'タカハシ',
            'kana02' => 'シンイチ',
        ),
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
            'tel01' => '012',
            'tel02' => '345',
            'tel03' => '6789',
        ),
        'email' => 'eccube@example.com',
        'contents' => 'お問い合わせ内容テスト',
    );

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('contact', null, array(
                'csrf_protection' => false,
            ))
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidNam01_NotBlank()
    {
        $this->formData['name']['name01'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidNam02_NotBlank()
    {
        $this->formData['name']['name02'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidKana01_NotBlank()
    {
        $this->formData['kana']['kana01'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidKana02_NotBlank()
    {
        $this->formData['kana']['kana02'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip01_LengthMin()
    {
        $this->formData['zip']['zip01'] = '1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip01_LengthMax()
    {
        $this->formData['zip']['zip01'] = '1234';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip02_LengthMin()
    {
        $this->formData['zip']['zip02'] = '1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip02_LengthMax()
    {
        $this->formData['zip']['zip02'] = '12345';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPref_Invalid()
    {
        $this->formData['address']['pref'] = '100';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel01_LengthMin()
    {
        $this->formData['tel']['tel01'] = '1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel01_LengthMax()
    {
        $this->formData['tel']['tel01'] = '12345';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel02_LengthMin()
    {
        $this->formData['tel']['tel02'] = '1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel02_LengthMax()
    {
        $this->formData['tel']['tel02'] = '12345';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel03_LengthMin()
    {
        $this->formData['tel']['tel03'] = '1';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel03_LengthMax()
    {
        $this->formData['tel']['tel03'] = '12345';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail_NotBlank()
    {
        $this->formData['email'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail_Invalid()
    {
        $this->formData['email'] = 'sample.example.com';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidContents_NotBlank()
    {
        $this->formData['contents'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

}
