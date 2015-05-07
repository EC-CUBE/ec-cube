<?php

namespace Eccube\Tests\Web;

class ProductControllerTest extends AbstractWebTestCase
{

    public function testRoutingList()
    {
        $client = $this->createClient();
        $client->request('GET', $this->app['url_generator']->generate('product_list'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

    public function testRoutingDetail()
    {
        $client = $this->createClient();
        $client->request('GET', $this->app['url_generator']->generate('product_detail', array('productId' => '1')));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
