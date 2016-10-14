<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2016 LOCKON CO.,LTD. All Rights Reserved.
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

namespace Eccube\Tests\Form\Type\Admin;

class MainEditTestType extends \Eccube\Tests\Form\Type\AbstractTypeTestCase
{
    /** @var \Eccube\Application */
    protected $app;

    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = array(
        'name' => 'テストページ',
        'url' => 'test',
        'file_name' => 'foo/bar/baz',
        'tpl_data' => 'contents',
        'author' => '',
        'description' => '',
        'keyword' => '',
        'meta_robots' => '',
        'DeviceType' => '10',
    );

    public function setUp()
    {
        parent::setUp();

        $options = array(
            'csrf_protection' => false,
        );
        $this->form = $this->app['form.factory']
            ->createBuilder('main_edit', null, $options)
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidName_Blank()
    {
        $this->formData['name'] = '';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidName_MaxLength()
    {
        $this->formData['name'] = str_repeat('1', $this->app['config']['stext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidUrl_Slash()
    {
        $this->formData['url'] = 'hoge/fuga/piyo';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidUrl_Symbol()
    {
        $this->formData['url'] = '-_';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidUrl_Blank()
    {
        $this->formData['url'] = '';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidUrl_StartsWithSlash()
    {
        $this->formData['url'] = '/hoge/fuga/piyo';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidUrl_EndsWithSlash()
    {
        $this->formData['url'] = 'hoge/fuga/piyo/';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidUrl_ContinuousSlash()
    {
        $this->formData['url'] = 'hoge/fuga//piyo';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidUrl_MaxLength()
    {
        $this->formData['url'] = str_repeat('1', $this->app['config']['stext_len'] + 1);;
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidUrl_DuplicateUrl()
    {
        $PageLayout = $this->createPageLayout();
        $this->formData['url'] = $PageLayout->getUrl();
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidFileName_Slash()
    {
        $this->formData['file_name'] = 'hoge/fuga/piyo';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidFileName_Symbol()
    {
        $this->formData['file_name'] = '-_';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidFileName_EndsWithSlash()
    {
        $this->formData['file_name'] = 'hoge/fuga/piyo/';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidFileName_Blank()
    {
        $this->formData['file_name'] = '';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidFileName_StartsWithSlash()
    {
        $this->formData['file_name'] = '/hoge/fuga/piyo';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidFileName_ContinuousSlash()
    {
        $this->formData['file_name'] = 'hoge/fuga//piyo';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidFileName_DuplicateSlash()
    {
        $this->formData['file_name'] = 'hoge/fuga//piyo';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidFileName_MaxLength()
    {
        $this->formData['file_name'] = str_repeat('1', $this->app['config']['stext_len'] + 1);;
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidTplData_Blank()
    {
        $this->formData['tpl_data'] = '';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidAuthor_Blank()
    {
        $this->formData['author'] = '';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidAuthor_MaxLength()
    {
        $this->formData['author'] = str_repeat('1', $this->app['config']['stext_len'] + 1);;
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidDescription_Blank()
    {
        $this->formData['description'] = '';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidDescription_MaxLength()
    {
        $this->formData['description'] = str_repeat('1', $this->app['config']['stext_len'] + 1);;
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidKeyword_Blank()
    {
        $this->formData['keyword'] = '';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidKeyword_MaxLength()
    {
        $this->formData['keyword'] = str_repeat('1', $this->app['config']['stext_len'] + 1);;
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidMetaRobots_Blank()
    {
        $this->formData['meta_robots'] = '';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidMetaRobots_MaxLength()
    {
        $this->formData['meta_robots'] = str_repeat('1', $this->app['config']['stext_len'] + 1);;
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
