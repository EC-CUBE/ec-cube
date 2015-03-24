<?php
namespace Eccube\Tests\Web;

use Silex\WebTestCase;
use Eccube\Application;

class PointControllerTest extends WebTestCase
{
    public $app;

    public function createApplication()
    {
        $app = new Application();
        $this->app = $app;

        $app['debug'] = true;
        $app['session.test'] = true;
        $app['exception_handler']->disable();

        return $app;
    }

    public function testRoutingAdminBasisPoint()
    {
        $client = $this->createClient();
        $crawler = $client->request('GET', '/admin/basis/point');
        $this->assertTrue($client->getResponse()->isSuccessful());
    }
}
