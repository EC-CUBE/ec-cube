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

}
