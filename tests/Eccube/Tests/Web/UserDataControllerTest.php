<?php

namespace Eccube\Tests\Web;

use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\PageLayout;
use org\bovigo\vfs\vfsStream;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserDataControllerTest extends AbstractWebTestCase
{
    protected $fileName = 'example_page';

    public function setUp()
    {
        parent::setUp();
        $root = vfsStream::setup('rootDir');
        vfsStream::newDirectory('user_data');
        // 404ページ表示のためにerror.twigを用意します、内容はダッミーです。
        vfsStream::newFile('error.twig')->at($root)->setContent('Error 404');

        // 一旦別の変数に代入しないと, config 以下の値を書きかえることができない
        $config = $this->app['config'];
        $config['template_default_realdir'] = vfsStream::url('rootDir');
        $config['user_data_realdir'] = $config['template_default_realdir'].'/user_data';
        mkdir($config['user_data_realdir']);

        $this->app['config'] = $config;

        $this->DeviceType = $this->app['orm.em']
            ->getRepository('Eccube\Entity\Master\DeviceType')
            ->find(DeviceType::DEVICE_TYPE_PC);

        $PageLayout = new PageLayout();
        $PageLayout
            ->setUrl($this->fileName)
            ->setFileName($this->fileName)
            ->setDeviceType($this->DeviceType)
            ->setEditFlg(PageLayout::EDIT_FLG_USER);
        $this->app['orm.em']->persist($PageLayout);
        $this->app['orm.em']->flush();

    }

    public function testIndex()
    {
        file_put_contents(
            $this->app['config']['user_data_realdir'].'/'.$this->fileName.'.twig',
            '<h1>test</h1>'
        );

        $client = $this->createClient();
        $crawler = $client->request(
            'GET',
            '/user_data/'.$this->fileName
        );
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->expected = 'test';
        $this->actual = $crawler->filter('h1')->text();
        $this->verify();
    }

    public function testIndexWithNotFound()
    {
        // debugはONの時に404ページ表示しない例外になります。
        if($this->app['debug'] == true){
            $this->setExpectedException('\Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
        }
        $client = $this->createClient();
        $crawler = $client->request(
            'GET',
            '/user_data/aaa'
        );
        // debugはOFFの時に404ページが表示します。
        if($this->app['debug'] == false){
            $this->expected = 404;
            $this->actual = $client->getResponse()->getStatusCode();
            $this->verify();
        }
    }
}
