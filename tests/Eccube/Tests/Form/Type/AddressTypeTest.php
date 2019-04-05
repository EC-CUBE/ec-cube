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


namespace Eccube\Tests\Form\Type;

class AddressTypeTest extends AbstractTypeTestCase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'address' => array(
            'pref' => '5',
            'addr01' => '北区',
            'addr02' => '梅田',
        ),
    );

    public function setUp()
    {
        parent::setUp();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->app['form.factory']
            ->createBuilder('form', null, array(
                'csrf_protection' => false,
            ))
            ->add('address', 'address')
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }


    public function testInvalidData_Addr01_MaxLength()
    {
        $data = array(
            'address' => array(
                'pref' => '1',
                'addr01' => str_repeat('ア', $this->app['config']['address1_len']+1),
                'addr02' => 'にゅうりょく',
            ));

        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidData_Addr02_MaxLength()
    {
        $data = array(
            'address' => array(
                'pref' => '1',
                'addr01' => 'にゅうりょく',
                'addr02' => str_repeat('ア', $this->app['config']['address2_len']+1),
            ));

        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidData_Pref_String()
    {
        $this->formData['address']['pref'] = 'aa';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidData_Pref_NonexistentValue()
    {
        $this->formData['address']['pref'] = '99'; // smallint以上の値だとpostgresが落ちる

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testRequiredAddNotBlank_Pref()
    {
        $this->form = $this->app['form.factory']
            ->createBuilder('form', null, array(
                'csrf_protection' => false,
            ))
            ->add('address', 'address', array(
                'required' => true,
            ))
            ->getForm();


        $this->formData['address']['pref'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testRequiredAddNotBlank_Addr01()
    {
        $this->form = $this->app['form.factory']
            ->createBuilder('form', null, array(
                'csrf_protection' => false,
            ))
            ->add('address', 'address', array(
                'required' => true,
            ))
            ->getForm();


        $this->formData['address']['addr01'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testRequiredAddNotBlank_Addr02()
    {
        $this->form = $this->app['form.factory']
            ->createBuilder('form', null, array(
                'csrf_protection' => false,
            ))
            ->add('address', 'address', array(
                'required' => true,
            ))
            ->getForm();


        $this->formData['address']['addr02'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
