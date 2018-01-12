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

use Eccube\Form\Type\RepeatedEmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;

class RepeatedEmailTypeTest extends AbstractTypeTestCase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'email' => array(
            'first' =>'eccube@example.com',
            'second' =>'eccube@example.com',
        ),
    );

    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();

        $this->form = $this->app['form.factory']
            ->createBuilder(FormType::class, null, ['csrf_protection' => false])
            ->add('email', RepeatedEmailType::class, array(
            ))
            ->getForm();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->form = null;
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInvalid_NotSameValue()
    {
        $this->formData['email']['second'] = 'eccube3@example.com';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_NotBlank()
    {
        $this->formData['email']['first'] = '';
        $this->formData['email']['second'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail_Nihongo()
    {
        $this->formData['email']['first'] = 'あいうえお@example.com';
        $this->formData['email']['second'] = 'あいうえお@example.com';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidEmail_RFC2822()
    {
        $this->formData['email']['first'] = 'abc..@example.com';
        $this->formData['email']['second'] = 'abc..@example.com';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }
}
