<?php

namespace Eccube\Tests\Web;

class CartControllerTest extends AbstractWebTestCase
{

    public function testRoutingCart()
    {
        $client = $this->createClient();
        $client->request('GET', '/cart');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRoutingCartAdd()
    {
        $client = $this->createClient();
        $client->request('POST', '/cart/add', array('product_class_id' => 1));

        $this->assertTrue($client->getResponse()->isRedirection());
    }

    public function testRoutingCartUp()
    {
        $client = $this->createClient();
        $client->request('GET', '/cart/up/1');
        $this->assertTrue($client->getResponse()->isRedirection());
    }

    public function testRoutingCartDown()
    {
        $client = $this->createClient();
        $client->request('GET', '/cart/down/1');
        $this->assertTrue($client->getResponse()->isRedirection());
    }

    public function testRoutingCartSetQuantity()
    {
        $client = $this->createClient();
        $client->request('GET', '/cart/setQuantity/2/1');
        $this->assertTrue($client->getResponse()->isRedirection());
    }

    public function testRoutingCartRemove()
    {
        $client = $this->createClient();
        $client->request('GET', '/cart/remove/1');
        $this->assertTrue($client->getResponse()->isRedirection());
    }
}
