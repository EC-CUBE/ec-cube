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

class RepeatedPasswordTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    public $config = array('password_min_len' => 8, 'password_max_len' => '32');

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'password' => array(
            'first' =>'eccube@example.com',
            'second' =>'eccube@example.com',
        ),
    );

    public function setUp()
    {
        parent::setUp();

        $app = $this->createApplication();
        // CSRF tokenを無効にしてFormを作成
        $this->form = $app['form.factory']->createBuilder('form', null, array('csrf_protection' => false))
            ->add('password', 'repeated_password', array(
            ))
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

    public function testInvalid_NotSameValue()
    {
        $this->formData['password']['second'] = 'eccube3@example.com';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_NotBlank()
    {
        $this->formData['password']['first'] = '';
        $this->formData['password']['second'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }


    public function testInvalid_LengthMin()
    {
        $password = str_repeat('1', $this->config['password_min_len']-1);

        $this->formData['password']['first'] = $password;
        $this->formData['password']['second'] = $password;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_LengthMax()
    {
        $password = str_repeat('1', $this->config['password_max_len']+1);

        $this->formData['password']['first'] = $password;
        $this->formData['password']['second'] = $password;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalid_Hiragana()
    {
        $password = str_repeat('あ', $this->config['password_max_len']);

        $this->formData['password']['first'] = $password;
        $this->formData['password']['second'] = $password;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    /* 環境依存で通るっぽい
    public function testValid_ZenkakuAlpha()
    {
        // これ通っていいのかな?
        $password = str_repeat('Ａ', $this->config['password_max_len']);

        $this->formData['password']['first'] = $password;
        $this->formData['password']['second'] = $password;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }
     */

    public function testInvalid_SpaceOnly()
    {
        $password = str_repeat(' ', $this->config['password_max_len']);

        $this->formData['password']['first'] = $password;
        $this->formData['password']['second'] = $password;
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testValid_Space()
    {
        // これも通っていいのか？
        $password = '1234 \n\s\t78';

        $this->formData['password']['first'] = $password;
        $this->formData['password']['second'] = $password;
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function createApplication()
    {
        $app = new \Silex\Application();
        $app->register(new \Silex\Provider\FormServiceProvider());
        $app->register(new \Eccube\ServiceProvider\ValidatorServiceProvider());
        $app['eccube.service.plugin'] = $app->share(function () use ($app) {
            return new \Eccube\Service\PluginService($app);
        });

        // fix php5.3
        $self = $this;
        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app, $self) {
            $types[] = new \Eccube\Form\Type\RepeatedPasswordType($self->config);

            return $types;
        }));

        return $app;
    }
}
