<?php

namespace Eccube\Tests\Web;

use Silex\WebTestCase;
use Eccube\Application;

class HelpControllerTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    public function createApplication()
    {
        $app = new Application(array(
            'env' => 'test',
        ));

        return $app;
    }

    /**
     * 特定商取引法のテスト
     */
    public function testRoutingHelpTradelaw()
    {
        $client = $this->createClient();
        $client->request('GET', $this->app['url_generator']->generate('help_tradelaw'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * 当サイトについてのテスト
     */
    public function testRoutingHelpAbout()
    {
        $client = $this->createClient();
        $client->request('GET', $this->app['url_generator']->generate('help_about'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * ご利用ガイドのテスト
     */
    public function testRoutingHelpGuide()
    {
        $client = $this->createClient();
        $client->request('GET', $this->app['url_generator']->generate('help_guide'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    /**
     * プライバシーポリシーのテスト
     */
    public function testRoutingHelpPrivacy()
    {
        $client = $this->createClient();
        $client->request('GET', $this->app['url_generator']->generate('help_privacy'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
