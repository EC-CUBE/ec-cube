<?php

namespace Eccube\Tests\Twig\Extension;

use Eccube\Tests\EccubeTestCase;
use org\bovigo\vfs\vfsStream;

class FunctionsTest extends EccubeTestCase
{
    protected $twigFileName;
    protected $fixture;
    protected $filter;

    public function setUp()
    {
        parent::setUp();
        $this->client = $this->createClient();
        $this->twigFileName = 'index';
        $root = vfsStream::setup('rootDir');

        // 一旦別の変数に代入しないと, config 以下の値を書きかえることができない
        $config = $this->app['config'];
        $config['template_realdir'] = vfsStream::url('rootDir');
        $this->app['config'] = $config;
    }

    /**
     * PHP関数を使用するテスト
     */
    public function testPhpFunctions()
    {
        $this->filter = '#test';
        $this->fixture = "<div id='test'>{{ php_print_r('aaa', true) }}</div>";
        $this->expected = 'aaa';

        $this->verify();
    }


    /**
     * fixture をテンプレートに書き出し, filter のノードと比較する.
     *
     * @param string $message
     */
    public function verify($message = null)
    {
        file_put_contents($this->app['config']['template_realdir'].'/'.$this->twigFileName.'.twig', $this->fixture);
        $crawler = $this->client->request('GET', $this->app->url('homepage'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->actual = $crawler->filter($this->filter)->html();
        parent::verify($message);
    }
}