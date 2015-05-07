<?php
namespace Eccube\Tests\Service;

use Eccube\Application;

class SystemServiceTest extends \PHPUnit_Framework_TestCase
{
    private $app;

    private $system;

    public function setUp()
    {
        $this->app = new Application(array(
            'env' => 'test'
        ));
        $this->app->boot();
    }

    public function testgetDbversion()
    {

        $system = $this->app['eccube.service.system'];
        $this->assertNotNull($system->getDbversion());

    }
}
