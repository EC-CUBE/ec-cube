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

namespace Eccube\Tests\Form\Type\Master;

class ZipTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    public $config = array('zip01_len' => 3, 'zip02_len' => 4);

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'zip' => array(
            'zip01' => '530',
            'zip02' => '0001',
        ),
    );

    public function setUp()
    {
        parent::setUp();

        $app = $this->createApplication();
        // CSRF tokenを無効にしてFormを作成
        $this->form = $app['form.factory']->createBuilder('form', null, array('csrf_protection' => false))
            ->add('zip', 'zip')
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);

        $this->assertTrue($this->form->isValid());
    }

    public function testInvalidZip01_LengthMin()
    {
        $this->formData['zip']['zip01'] = str_repeat('1', $this->config['zip01_len']-1);
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip01_LengthMax()
    {
        $this->formData['zip']['zip01'] = str_repeat('1', $this->config['zip01_len']+1);
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip02_LengthMin()
    {
        $this->formData['zip']['zip02'] = str_repeat('1', $this->config['zip02_len']-1);
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidZip02_LengthMax()
    {
        $this->formData['zip']['zip02'] = str_repeat('1', $this->config['zip02_len']+1);
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }


    public function testRequiredAddNotBlank_Zip01()
    {
        $app = $this->createApplication();
        $this->form = $app['form.factory']->createBuilder('form', null, array('csrf_protection' => false))
            ->add('zip', 'zip', array(
                'required' => true,
            ))
            ->getForm();

        $this->formData['zip']['zip01'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testRequiredAddNotBlank_Zip02()
    {
        $app = $this->createApplication();
        $this->form = $app['form.factory']->createBuilder('form', null, array('csrf_protection' => false))
            ->add('zip', 'zip', array(
                'required' => true,
            ))
            ->getForm();

        $this->formData['zip']['zip02'] = '';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
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
            $types[] = new \Eccube\Form\Type\ZipType($self->config);

            return $types;
        }));

        return $app;
    }
}
