<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Form\Type\Admin;

use Eccube\Form\Type\Admin\MainEditType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;

class MainEditTypeTest extends AbstractTypeTestCase
{
    /** @var \Symfony\Component\Form\FormInterface */
    protected $form;

    /** @var array デフォルト値（正常系）を設定 */
    protected $formData = [
        'name' => 'テストページ',
        'url' => 'test',
        'file_name' => 'foo/bar/baz',
        'tpl_data' => 'contents',
        'author' => '',
        'description' => '',
        'keyword' => '',
        'meta_robots' => '',
        'meta_tags' => '',
    ];

    public function setUp()
    {
        parent::setUp();
        $options = [
            'csrf_protection' => false,
        ];

        $this->form = $this->formFactory
            ->createBuilder(MainEditType::class, $this->createPage(), $options)
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
        $this->formData['name'] = str_repeat('1', $this->eccubeConfig['eccube_stext_len'] + 1);
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
        $this->formData['url'] = str_repeat('1', $this->eccubeConfig['eccube_stext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidUrl_DuplicateUrl()
    {
        $Page = $this->createPage();
        $this->formData['url'] = $Page->getUrl();
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
        $this->formData['file_name'] = str_repeat('1', $this->eccubeConfig['eccube_stext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidTplData_Blank()
    {
        $this->formData['tpl_data'] = '';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidAuthor_Blank()
    {
        $this->formData['author'] = '';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidAuthor_MaxLength()
    {
        $this->formData['author'] = str_repeat('1', $this->eccubeConfig['eccube_stext_len'] + 1);
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
        $this->formData['description'] = str_repeat('1', $this->eccubeConfig['eccube_stext_len'] + 1);
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
        $this->formData['keyword'] = str_repeat('1', $this->eccubeConfig['eccube_stext_len'] + 1);
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
        $this->formData['meta_robots'] = str_repeat('1', $this->eccubeConfig['eccube_stext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidMetaTags_Blank()
    {
        $this->formData['meta_tags'] = '';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidMetaTags_FreeLength()
    {
        $this->formData['meta_tags'] = '<meta name="meta_tags_test" content="test" />';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidMetaTags_MaxLength()
    {
        $this->formData['meta_tags'] = str_repeat('1', $this->eccubeConfig['eccube_lltext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
