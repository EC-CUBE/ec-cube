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

class CustomerTypeTest extends AbstractTypeTestCase
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
        'kana'=> array(
            'kana01' => 'タカハシ',
            'kana02' => 'シンイチ',
        ),
        'company_name' => 'ロックオン',
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
        'fax' => array(
            'fax01' => '112',
            'fax02' => '345',
            'fax03' => '6789',
        ),
        'email' => 'default@example.com',
        'sex' => 1,
        'job' => 1,
        'birth' => array(
            'year' => '1983',
            'month' => '2',
            'day' => '14',
        ),
        'password' => array(
            'first' => 'password',
            'second' => 'password',
        ),
    );

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('customer', null, array(
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
        $this->formData['name01'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidNam02_NotBlank()
    {
        $this->formData['name02'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidKana01_NotBlank()
    {
        $this->formData['kana01'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidKana02_NotBlank()
    {
        $this->formData['kana02'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail_NotBlank()
    {
        $this->formData['email'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail_InvalidEmail()
    {
        $this->formData['email'] = 'sample.example.com';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail_Duplicate()
    {
        $testVal = 'sampleexample.com';
        $customer = $this->app['eccube.repository.customer']->newCustomer()
            ->setName01($this->formData['name']['name01'])
            ->setName02($this->formData['name']['name02'])
            ->setKana01($this->formData['kana']['kana01'])
            ->setKana02($this->formData['kana']['kana02'])
            ->setPassword($this->formData['password']['first'])
            ->setEmail($testVal);

        $form = $this->app['form.factory']
            ->createBuilder('customer', $customer, array(
                'csrf_protection' => false,
            ))
            ->getForm();

        $this->formData['email'] = $testVal;
        $form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidPassword_Invalid()
    {
        $this->formData['password']['first'] = 'anotherPassword';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

}
