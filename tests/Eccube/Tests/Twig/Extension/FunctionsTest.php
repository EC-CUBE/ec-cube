<?php

namespace Eccube\Tests\Twig\Extension;

use Eccube\Tests\EccubeTestCase;
use org\bovigo\vfs\vfsStream;

class FunctionsTest extends EccubeTestCase
{
    protected $templateDir;
    protected $blockTwig = 'block.twig';

    public function setUp()
    {
        parent::setUp();

        $root = vfsStream::setup();
        $this->templateDir = $root->url();

        // テンプレート探索パスを追加
        $this->app['twig.loader']->addLoader(
            new \Twig_Loader_Filesystem(
                [
                    $this->templateDir,
                ]
            )
        );

        // ブロックテンプレートを初期化
        $this->app['eccube.twig.block.templates'] = [
            $this->blockTwig,
        ];
    }

    /**
     * PHP関数を使用するテスト
     */
    public function testPhpFunctions()
    {
        /** @var \Twig_Environment $twig */
        $twig = $this->app['twig'];
        $template = $twig->createTemplate("<div id='test'>{{ php_print_r('aaa', true) }}</div>");

        $this->expected = "<div id='test'>aaa</div>";
        $this->actual = $template->render([]);

        $this->verify();
    }

    /**
     * 任意のブロックをユーザー定義関数で出力するテスト.
     */
    public function testEccubeBlockFunctions()
    {
        $file = $this->templateDir.'/'.$this->blockTwig;
        $source = '{% block exampleblock %}<div id="exampleblock">test</div>{% endblock %}';

        file_put_contents($file, $source);

        /** @var \Twig_Environment $twig */
        $twig = $this->app['twig'];
        $template = $twig->createTemplate("<div id='test'>{{ eccube_block_exampleblock() }}</div>");

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
        $file = $this->templateDir.'/'.$this->blockTwig;
        $source = '{% block exampleblock %}<div id="exampleblock">{{ variable }}</div>{% endblock %}';

        file_put_contents($file, $source);

        /** @var \Twig_Environment $twig */
        $twig = $this->app['twig'];
        $template = $twig->createTemplate(
            "<div id='test'>{{ eccube_block_exampleblock({'variable': 'example'}) }}</div>"
        );

        $this->expected = "<div id='test'><div id=\"exampleblock\">example</div></div>";
        $this->actual = $template->render(['variable' => 'example']);
    }
}
