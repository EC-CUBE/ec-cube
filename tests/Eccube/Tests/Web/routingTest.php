<?php
namespace Eccube\Tests\Web;

use Silex\WebTestCase;
use Eccube\Application;

class routingTest extends WebTestCase
{
    public function createApplication()
    {
        $app = new Application();

        $app['debug'] = true;
        $app['session.test'] = true;
        $app['exception_handler']->disable();

        return $app;
    }

    public function testRouting()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}