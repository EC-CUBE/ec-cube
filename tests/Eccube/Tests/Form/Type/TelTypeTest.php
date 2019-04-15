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

class TelTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    public $config = array('tel_len' => 5, 'tel_len_min' => 1);

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'tel' => array(
            'tel01' => '012',
            'tel02' => '3456',
            'tel03' => '6789',
        ),
    );

    /**
     * getValidTestData
     *
     * 正常系のデータパターンを返す
     *
     * @access public
     * @return array
     */
    public function getValidTestData()
    {
        return array(
            array(
                'data' => array(
                    'tel' => array(
                        'tel01' => '01',
                        'tel02' => '2345',
                        'tel03' => '6789',
                    ),
                ),
            ),
            array(
                'data' => array(
                    'tel' => array(
                        'tel01' => '1',
                        'tel02' => '2345',
                        'tel03' => '6789',
                    ),
                ),
            ),
            array(
                'data' => array(
                    'tel' => array(
                        'tel01' => '012',
                        'tel02' => '345',
                        'tel03' => '6789',
                    ),
                ),
            ),
            array(
                'data' => array(
                    'tel' => array(
                        'tel01' => '0124',
                        'tel02' => '56',
                        'tel03' => '7890',
                    ),
                ),
            ),
            array(
                'data' => array(
                    'tel' => array(
                        'tel01' => '01245',
                        'tel02' => '60',
                        'tel03' => '7890',
                    ),
                ),
            ),
            // 携帯,PHS
            array(
                'data' => array(
                    'tel' => array(
                        'tel01' => '090',
                        'tel02' => '1234',
                        'tel03' => '5678',
                    ),
                ),
            ),
            // フリーダイヤル
            array(
                'data' => array(
                    'tel' => array(
                        'tel01' => '0120',
                        'tel02' => '123',
                        'tel03' => '456',
                    ),
                ),
            ),
            array(
                'data' => array(
                    'tel' => array(
                        'tel01' => '０３',
                        'tel02' => '１２３４',
                        'tel03' => '５６７８',
                    ),
                ),
            ),
            // 全部空はOK
            array(
                'data' => array(
                    'tel' => array(
                        'tel01' => '',
                        'tel02' => '',
                        'tel03' => '',
                    ),
                ),
            ),
        );
    }

    public function setUp()
    {
        parent::setUp();

        $app = $this->createApplication();

        // CSRF tokenを無効にしてFormを作成
        $this->form = $app['form.factory']->createBuilder('form', null, array('csrf_protection' => false))
            ->add('tel', 'tel', array(
                'required' => false,
            ))
            ->getForm();
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->form = null;
    }

    /**
     * @dataProvider getValidTestData
     */
    public function testValidData($data)
    {
        $this->form->submit($data);
        $this->assertTrue($this->form->isValid());
    }


    public function testInvalidTel01_LengthMax()
    {
        $this->formData['tel']['tel01'] = '12345678';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel02_LengthMax()
    {
        $this->formData['tel']['tel02'] = '12345678';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel03_LengthMax()
    {
        $this->formData['tel']['tel03'] = '12345678';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel01_NotNumber()
    {
        $this->formData['tel']['tel01'] = 'aaaa';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel02_NotNumber()
    {
        $this->formData['tel']['tel02'] = 'aaaa';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidTel03_NotNumber()
    {
        $this->formData['tel']['tel03'] = 'aaaa';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }


    public function testInvalidTel_BlankOne()
    {
        $this->formData['tel']['tel01'] = '';
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
    }

    public function testSubmitFromZenToHan()
    {
        $input = array(
            'tel' => array(
                'tel01' => '１２３４５',
                'tel02' => '１２３４５',
                'tel03' => '６７８９０',
            ));

        $output = array(
            'tel01' => '12345',
            'tel02' => '12345',
            'tel03' => '67890',
        );

        $this->form->submit($input);
        $this->assertEquals($output, $this->form->getData());
    }

    public function testRequiredAddNotBlank_Tel()
    {
        $app = $this->createApplication();
        $this->form = $app['form.factory']->createBuilder('form', null, array('csrf_protection' => false))
            ->add('tel', 'tel', array(
                'required' => true,
            ))
            ->getForm();

        $this->formData['tel']['tel01'] = '';
        $this->formData['tel']['tel02'] = '';
        $this->formData['tel']['tel03'] = '';

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
            $types[] = new \Eccube\Form\Type\TelType($self->config);

            return $types;
        }));

        return $app;
    }
}
