<?php

namespace Eccube\Tests\Web;

use Silex\WebTestCase;
use Eccube\Application;

class HelpControllerTest extends WebTestCase
{

    public function createApplication()
    {
        $app = new Application();

        $app['debug'] = true;
        $app['session.test'] = true;
        $app['exception_handler']->disable();

        return $app;
    }

    /**
     * 特定商取引法のテスト
     */
    public function testRoutingHelpTradelaw()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/help/tradelaw/');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * 当サイトについてのテスト
     */
    public function testRoutingHelpAbout()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/help/about/');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * ご利用ガイドのテスト
     */
    public function testRoutingHelpGuide()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/help/guide/');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * プライバシーポリシーのテスト
     */
    public function testRoutingHelpPrivacy()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/help/privacy/');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

}
