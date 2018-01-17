<?php

namespace Eccube\Tests\Web;

use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Page;
use org\bovigo\vfs\vfsStream;

class UserDataControllerTest extends AbstractWebTestCase
{
    protected $fileName = 'example_page';

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        parent::setUp();

        $this->twig = $this->container->get('twig');

        $root = vfsStream::setup('rootDir');
        vfsStream::newDirectory('user_data');
        // 404ページ表示のためにerror.twigを用意します、内容はダミーです。
        vfsStream::newFile('error.twig')->at($root)->setContent('Error 404');

        // 一旦別の変数に代入しないと, config 以下の値を書きかえることができない
        $this->eccubeConfig['template_default_realdir'] = vfsStream::url('rootDir');
        $this->eccubeConfig['user_data_realdir'] = $this->eccubeConfig['template_default_realdir'].'/user_data';
        mkdir($this->eccubeConfig['user_data_realdir']);

        // $this->app->overwrite('config', $config);
        // add path to user_data alias of twig, make twig can find template file
        $this->twig->getLoader()->addPath($this->eccubeConfig['user_data_realdir'], 'user_data');

        $deviceType = $this->entityManager
            ->getRepository(\Eccube\Entity\Master\DeviceType::class)
            ->find(DeviceType::DEVICE_TYPE_PC);
        if ($deviceType) {
            $page = new Page();
            $page->setUrl($this->fileName)
                ->setFileName($this->fileName)
                ->setDeviceType($deviceType)
                ->setEditType(Page::EDIT_TYPE_USER);
            $this->entityManager->persist($page);
            $this->entityManager->flush();
        }
    }

    public function testIndex()
    {
        file_put_contents(
            $this->eccubeConfig['user_data_realdir'] . '/' . $this->fileName . '.twig',
            '<h1>test</h1>'
        );

        $crawler = $this->client->request(
            'GET',
            '/user_data/'.$this->fileName
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $this->expected = 'test';
        $this->actual = $crawler->filter('h1')->text();
        $this->verify();
    }

    public function testIndexWithNotFound()
    {
        // $isDebug = $this->container->getParameter('kernel.debug');
        // debugはONの時に404ページ表示しない例外になります。
        // if ($isDebug) {
        //    $this->expectException('\Symfony\Component\HttpKernel\Exception\NotFoundHttpException');
        //}
        $this->client->request(
            'GET',
            '/user_data/aaa'
        );
        // debugはOFFの時に404ページが表示します。
        // if (!$isDebug) {
        $this->expected = 404;
        $this->actual = $this->client->getResponse()->getStatusCode();
        $this->verify();
        //}
    }
}
