<?php

namespace Eccube\Tests\Web;

class EntryControllerTest extends AbstractWebTestCase
{

    public function testRoutingIndex()
    {
        $client = $this->createClient();
        $client->request('GET', $this->app['url_generator']->generate('entry'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRoutingComplete()
    {
        $client = $this->createClient();
        $client->request('GET', $this->app['url_generator']->generate('entry_complete'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
