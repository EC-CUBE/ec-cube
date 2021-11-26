<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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

    public function testInValidNameBlank()
    {
        $this->formData['name'] = '';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidNameMaxLength()
    {
        $this->formData['name'] = str_repeat('1', $this->eccubeConfig['eccube_stext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidUrlSlash()
    {
        $this->formData['url'] = 'hoge/fuga/piyo';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidUrlSymbol()
    {
        $this->formData['url'] = '-_';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidUrlBlank()
    {
        $this->formData['url'] = '';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidUrlStartsWithSlash()
    {
        $this->formData['url'] = '/hoge/fuga/piyo';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidUrlEndsWithSlash()
    {
        $this->formData['url'] = 'hoge/fuga/piyo/';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidUrlContinuousSlash()
    {
        $this->formData['url'] = 'hoge/fuga//piyo';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidUrlMaxLength()
    {
        $this->formData['url'] = str_repeat('1', $this->eccubeConfig['eccube_stext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidUrlDuplicateUrl()
    {
        $Page = $this->createPage();
        $this->formData['url'] = $Page->getUrl();
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidFileNameSlash()
    {
        $this->formData['file_name'] = 'hoge/fuga/piyo';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidFileNameSymbol()
    {
        $this->formData['file_name'] = '-_';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidFileNameEndsWithSlash()
    {
        $this->formData['file_name'] = 'hoge/fuga/piyo/';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidFileNameBlank()
    {
        $this->formData['file_name'] = '';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidFileNameStartsWithSlash()
    {
        $this->formData['file_name'] = '/hoge/fuga/piyo';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidFileNameContinuousSlash()
    {
        $this->formData['file_name'] = 'hoge/fuga//piyo';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidFileNameDuplicateSlash()
    {
        $this->formData['file_name'] = 'hoge/fuga//piyo';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidFileNameMaxLength()
    {
        $this->formData['file_name'] = str_repeat('1', $this->eccubeConfig['eccube_stext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidTplDataBlank()
    {
        $this->formData['tpl_data'] = '';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidAuthorBlank()
    {
        $this->formData['author'] = '';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidAuthorMaxLength()
    {
        $this->formData['author'] = str_repeat('1', $this->eccubeConfig['eccube_stext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidDescriptionBlank()
    {
        $this->formData['description'] = '';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidDescriptionMaxLength()
    {
        $this->formData['description'] = str_repeat('1', $this->eccubeConfig['eccube_stext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidKeywordBlank()
    {
        $this->formData['keyword'] = '';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidKeywordMaxLength()
    {
        $this->formData['keyword'] = str_repeat('1', $this->eccubeConfig['eccube_stext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidMetaRobotsBlank()
    {
        $this->formData['meta_robots'] = '';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidMetaRobotsMaxLength()
    {
        $this->formData['meta_robots'] = str_repeat('1', $this->eccubeConfig['eccube_stext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidMetaTagsBlank()
    {
        $this->formData['meta_tags'] = '';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidMetaTagsFreeLength()
    {
        $this->formData['meta_tags'] = '<meta name="meta_tags_test" content="test" />';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidMetaTagsMaxLength()
    {
        $this->formData['meta_tags'] = str_repeat('1', $this->eccubeConfig['eccube_ltext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }
}
