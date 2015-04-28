<?php

namespace Eccube\Tests\Web;

use Silex\WebTestCase;
use Eccube\Application;

class ShoppingControllerTest extends WebTestCase
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

    public function testRoutingShoppingLogin()
    {
        $client = $this->createClient();
        $client->request('GET', '/shopping/login/');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }

}
