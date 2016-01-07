<?php

namespace Eccube\Tests\Web;

class BlockControllerTest extends AbstractWebTestCase
{
    public function testIndex()
    {
        $client = $this->createClient();
        $crawler = $client->request(
            'GET',
            $this->app->path('block_search_product')
        );
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
