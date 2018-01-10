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

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Eccube\Form\Type\NameType;

class NameTypeTest extends AbstractTypeTestCase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    protected $maxLength = 50;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'name' => array(
            'name01' => 'たかはし',
            'name02' => 'しんいち',
        ),
    );

    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
        $this->form = $this->app['form.factory']
            ->createBuilder(FormType::class, null, ['csrf_protection' => false])
            ->add('name', NameType::class)
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

    public function testInvalidData_Name01_MaxLength()
    {
        $data = array(
            'name' => array(
                'name01' => str_repeat('ア', $this->maxLength+1),
                'name02' => 'にゅうりょく',
            ));

        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidData_Name02_MaxLength()
    {
        $data = array(
            'name' => array(
                'name01' => 'にゅうりょく',
                'name02' => str_repeat('ア', $this->maxLength+1),
            ));

        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidData_Name01_HasWhiteSpaceEn()
    {
        $data = array(
            'name' => array(
                'name01' => 'hoge hoge',
                'name02' => 'にゅうりょく',
            ));

        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidData_Name02_HasWhiteSpaceEn()
    {
        $data = array(
            'name' => array(
                'name01' => 'にゅうりょく',
                'name02' => 'hoge hoge',
            ));

        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidData_Name01_HasWhiteSpaceJa()
    {
        $data = array(
            'name' => array(
                'name01' => 'hoge　hoge',
                'name02' => 'にゅうりょく',
            ));

        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidData_Name02_HasWhiteSpaceJa()
    {
        $data = array(
            'name' => array(
                'name01' => 'にゅうりょく',
                'name02' => 'hoge　hoge',
            ));

        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }
}
