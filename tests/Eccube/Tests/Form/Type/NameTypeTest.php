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

class NameTypeTest extends \PHPUnit_Framework_TestCase
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
        parent::setUp();

        $app = new \Silex\Application();
        $app->register(new \Silex\Provider\FormServiceProvider());
        $app->register(new \Eccube\ServiceProvider\ValidatorServiceProvider());

        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $config['config']['name_len'] = 50;
            $types[] = new \Eccube\Form\Type\NameType($config['config']); // Nameに依存する
            return $types;
        }));

        // CSRF tokenを無効にしてFormを作成
        $this->form = $app['form.factory']->createBuilder('form', null, array('csrf_protection' => false))
            ->add('name', 'name')
            ->getForm();
    }

    protected function tearDown()
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
