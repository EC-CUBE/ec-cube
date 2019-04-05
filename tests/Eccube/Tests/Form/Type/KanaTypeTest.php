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

class KanaTypeTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    protected $maxLength = 50;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'kana' => array(
            'kana01' => 'たかはし',
            'kana02' => 'しんいち',
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
                    'kana' => array(
                        'kana01' => 'たかはし',
                        'kana02' => 'しんいち',
                    ),
                ),
            ),
            array(
                'data' => array(
                    'kana' => array(
                        'kana01' => 'タカハシ',
                        'kana02' => 'しんいち',
                    ),
                ),
            ),
            array(
                'data' => array(
                    'kana' => array(
                        'kana01' => 'たかはし',
                        'kana02' => 'シンイチ',
                    ),
                ),
            ),
            array(
                'data' => array(
                    'kana' => array(
                        'kana01' => str_repeat('ア', $this->maxLength),
                        'kana02' => str_repeat('ア', $this->maxLength),
                    ),
                ),
            ),
        );
    }

    public function setUp()
    {
        parent::setUp();

        $app = new \Silex\Application();
        $app->register(new \Silex\Provider\FormServiceProvider());
        $app->register(new \Eccube\ServiceProvider\ValidatorServiceProvider());

        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $config['config']['name_len'] = 50; // php5.3で落ちてしまうので...
            $config['config']['kana_len'] = 50;
            $types[] = new \Eccube\Form\Type\NameType($config['config']); // Nameに依存する
            $types[] = new \Eccube\Form\Type\KanaType($config['config']);
            return $types;
        }));

        // CSRF tokenを無効にしてFormを作成
        $this->form = $app['form.factory']->createBuilder('form', null, array('csrf_protection' => false))
            ->add('kana', 'kana')
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

    public function testInvalidData_Kana01_MaxLength()
    {
        $data = array(
            'kana' => array(
                'kana01' => str_repeat('ア', $this->maxLength+1),
                'kana02' => 'にゅうりょく',
            ));

        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }

    public function testInvalidData_Kana02_MaxLength()
    {
        $data = array(
            'kana' => array(
                'kana01' => 'にゅうりょく',
                'kana02' => str_repeat('ア', $this->maxLength+1),
            ));

        $this->form->submit($data);
        $this->assertFalse($this->form->isValid());
    }

    public function testinvaliddata_kana01_haswhitespaceEn()
    {
        $data = array(
            'kana' => array(
                'kana01' => 'ホゲ ホゲ',
                'kana02' => 'フガフガ',
            ));

        $this->form->submit($data);
        $this->assertfalse($this->form->isvalid());
    }

    public function testinvaliddata_kana02_haswhitespaceEn()
    {
        $data = array(
            'kana' => array(
                'kana01' => 'ホゲホゲ',
                'kana02' => 'フガ フガ',
            ));

        $this->form->submit($data);
        $this->assertfalse($this->form->isvalid());
    }

    public function testinvaliddata_kana01_haswhitespaceJa()
    {
        $data = array(
            'kana' => array(
                'kana01' => 'ホゲ　ホゲ',
                'kana02' => 'フガフガ',
            ));

        $this->form->submit($data);
        $this->assertfalse($this->form->isvalid());
    }

    public function testinvaliddata_kana02_haswhitespaceJa()
    {
        $data = array(
            'kana' => array(
                'kana01' => 'ホゲホゲ',
                'kana02' => 'フガ　フガ',
            ));

        $this->form->submit($data);
        $this->assertfalse($this->form->isvalid());
    }

    /**
     * ひらがな入力されてもカタカナで返す
     */
    public function testSubmitFromHiraganaToKana()
    {
        $input = array(
            'kana' => array(
                'kana01' => 'ひらがな',
                'kana02' => 'にゅうりょく',
            ));

        $output = array(
            'kana01' => 'ヒラガナ',
            'kana02' => 'ニュウリョク',
        );

        $this->form->submit($input);
        $this->assertEquals($output, $this->form->getData());
    }
}
