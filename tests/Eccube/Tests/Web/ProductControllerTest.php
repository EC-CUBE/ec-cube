<?php

namespace Eccube\Tests\Web;

use Silex\WebTestCase;
use Eccube\Application;

class ProductControllerTest extends WebTestCase
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

        $client->request('GET', $this->app['url_generator']->generate('product_detail', array('productId' => '999999999')));
        $this->assertFalse($client->getResponse()->isSuccessful());
    }

    public function testRoutingDetailFail()
    {
        $client = $this->createClient();
        $client->request('GET', $this->app['url_generator']->generate('product_detail', array('productId' => '999999999')));
        $this->assertFalse($client->getResponse()->isSuccessful());
    }

}
