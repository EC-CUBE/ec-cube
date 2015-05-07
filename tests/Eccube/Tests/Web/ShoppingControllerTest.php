<?php

namespace Eccube\Tests\Web;

class ShoppingControllerTest extends AbstractWebTestCase
{

    public function testRoutingShoppingLogin()
    {
        $client = $this->createClient();
        $client->request('GET', '/shopping/login/');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
