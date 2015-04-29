<?php
namespace Eccube\Tests\Web;

use Silex\WebTestCase;
use Eccube\Application;

class BasisControllerTest extends WebTestCase
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

    public function testRoutingAdminBasis()
    {
        self::markTestSkipped();

        $client = $this->createClient();
        $client->request('GET', $this->app['url_generator']->generate('admin_basis'));
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
