<?php
namespace Eccube\Tests\Web;

use Silex\WebTestCase;
use Eccube\Application;

class PointControllerTest extends WebTestCase
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

    public function testRoutingAdminBasisPoint()
    {
        self::markTestSkipped();

        $client = $this->createClient();
        $crawler = $client->request('GET', '/admin/basis/point');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
