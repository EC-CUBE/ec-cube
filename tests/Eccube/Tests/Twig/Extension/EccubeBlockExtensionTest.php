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

use Eccube\Tests\EccubeTestCase;
use Eccube\Twig\Extension\EccubeBlockExtension;
use org\bovigo\vfs\vfsStream;

class EccubeBlockExtensionTest extends EccubeTestCase
{
    protected $templateDir;

    protected $blockTwigs = [
        'test_block.twig',
        'test_block2.twig',
    ];

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function setUp()
    {
        parent::setUp();

        $root = vfsStream::setup();
        $this->templateDir = $root->url();

        foreach ($this->blockTwigs as $twig_file) {
            // Preventing undefined errors
            file_put_contents($this->templateDir.'/'.$twig_file, '');
        }

        $loader = new \Twig_Loader_Filesystem([
            $this->templateDir,
        ]);
        $this->twig = new \Twig_Environment($loader);
        $this->twig->addExtension(new EccubeBlockExtension($this->twig, $this->blockTwigs));
    }

    /**
     * 任意のブロックをユーザー定義関数で出力するテスト.
     */
    public function testEccubeBlockFunctions()
    {
        $file = $this->templateDir.'/'.$this->blockTwigs[0];
        $source = '{% block exampleblock %}<div id="exampleblock">test</div>{% endblock %}';

        file_put_contents($file, $source);
        $template = $this->twig->createTemplate("<div id='test'>{{ eccube_block_exampleblock() }}</div>");

        $this->expected = "<div id='test'><div id=\"exampleblock\">test</div></div>";
        $this->actual = $template->render([]);

        $this->verify();
    }

    /**
     * 任意のブロックをユーザー定義関数で出力するテスト.
     *
     * 引数付き
     */
    public function testEccubeBlockFunctionsWithParams()
    {
        $file = $this->templateDir.'/'.$this->blockTwigs[1];
        $source = '{% block exampleblock2 %}<div id="exampleblock">{{ variable }}</div>{% endblock %}';

        file_put_contents($file, $source);

        $template = $this->twig->createTemplate(
            "<div id='test'>{{ eccube_block_exampleblock2({'variable': 'example'}) }}</div>"
        );

        $this->expected = "<div id='test'><div id=\"exampleblock\">example</div></div>";
        $this->actual = $template->render(['variable' => 'example']);

        $this->verify();
    }
}
