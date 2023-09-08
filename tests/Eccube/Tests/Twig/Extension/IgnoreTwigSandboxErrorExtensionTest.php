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

namespace Eccube\Tests\Twig\Extension;

use Eccube\Common\EccubeConfig;
//use Eccube\Tests\EccubeTestCase;
use Eccube\Tests\Web\AbstractWebTestCase;
use Eccube\Twig\Extension\ignoreTwigSandboxErrorExtension;
use Twig\Sandbox\SecurityError;
use org\bovigo\vfs\vfsStream;
use Twig\Extension\StringLoaderExtension;

class IgnoreTwigSandboxErrorExtensionTest extends AbstractWebTestCase
{
     /**
     * @var ignoreTwigSandboxErrorExtension
     */
    protected $sandbox;

    /**
     * @var Twig\Environment
     */
    protected $twig;

    protected $templateDir;
    /**
     * @var String
     */
    protected $template = [
        'test_freearea.twig',
        'test_metatag.twig',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $EccubeConfig = static::getContainer()->get(EccubeConfig::class);

        // Twigを使用するテンプレートの読込み
        $root = vfsStream::setup();
        $this->templateDir = $root->url();

        foreach ($this->template as $twig_file) {
            // Preventing undefined errors
            file_put_contents($this->templateDir.'/'.$twig_file, '');
        }

        $loader = new \Twig\Loader\FilesystemLoader([
            $this->templateDir,
        ]);

        $tags = ['if', 'for', 'set', 'do'];
        $filters = ['escape', 'join', 'length', 'escape', 'date'];
        $functions = ['range'];

        $policy = new \Twig\Sandbox\SecurityPolicy($tags, $filters, [], [], $functions);
        $sandbox = new \Twig\Extension\SandboxExtension($policy, true);

        $this->twig = new \Twig\Environment($loader);
        $this->twig->addExtension(new \Twig\Extension\StringLoaderExtension());
        $this->twig->addExtension($sandbox);
    }

    public function twigKeyWords()
    {
        // 第1要素：入力値
        // 第2要素：成功か否か
        return [
            // Tag
            ['{{ random(1, 100) }}', true],
            ['{ "hello world"|upper }', true],
            ['{dump(app)}', false],
            ['{% do 1 + 2 %}', false],
        ];
    }

    /**
     * @dataProvider twigKeyWords
     */
    public function testIgnoreSandboxFreeArea($context, $expected)
    {
        $file = $this->templateDir.'/'.$this->template[0];
        $source = "<div>{{ include(template_from_string(" . $context . ")) }}</div>";

        file_put_contents($file, $source);

        $actual = true;
        try {
            ignoreTwigSandboxErrorExtension::twig_include($this->twig, [], $this->template[0], [], true, false, true);
        } catch (SecurityError $e) {
            $actual = false;
        }

        $this->assertSame($expected, $actual);
    }
}