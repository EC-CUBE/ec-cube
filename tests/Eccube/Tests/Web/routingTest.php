<?php
namespace Eccube\Tests\Web;
use Silex\WebTestCase;
use Eccube\Application;
class routingTest extends WebTestCase
{

    public $app;

    public function createApplication()
    {
        $app = new Application();
        $this->app = $app;

        $app['debug'] = true;
        $app['session.test'] = true;
        $app['exception_handler']->disable();

        return $app;
    }

    public function testRoutingContact()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/contact/');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRoutingEntry()
    {
        $referer = $this->app['url_generator']->generate('entry_kiyaku');

        $client = $this->createClient(array('HTTP_REFERER' => $referer));
        $crawler = $client->request('GET', '/entry/');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRoutingEntry_Redirect()
    {
        $client = $this->createClient(array('HTTP_REFERER' => ''));
        $client->followRedirects(false);
        $crawler = $client->request('GET', '/entry/');
        $this->assertTrue($client->getResponse()->isRedirect($this->app['url_generator']->generate('entry_kiyaku')));
    }

    // 特定商取引法のテスト
    public function testRoutingHelpTradelaw()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/help/tradelaw/');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
