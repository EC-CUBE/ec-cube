<?php

namespace Eccube\Tests\Web;

use Silex\WebTestCase;
use Eccube\Application;

class RoutingTest extends WebTestCase
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

    public function testRoutingIndex()
    {
        $client = $this->createClient();
        $client->request('GET', $this->app['url_generator']->generate('contact'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRoutingComplete()
    {
        $client = $this->createClient();
        $client->request('GET', $this->app['url_generator']->generate('contact_complete'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
