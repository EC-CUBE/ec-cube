<?php

namespace Eccube\Tests\Web;

class TopControllerTest extends AbstractWebTestCase
{

    public function testRoutingCart()
    {
        $client = $this->createClient();
        $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

}
