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

    public function testRoutingContact()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/contact/');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRoutingEntry()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/entry/');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

}
