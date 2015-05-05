<?php

namespace Eccube\Tests\Web;

use Silex\WebTestCase;
use Eccube\Application;

abstract class AbstractWebTestCase extends WebTestCase
{

    public $client = null;

    public function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->app['orm.em']->getConnection()->close();
        $this->app = null;
        $this->client = null;
    }

    /**
     * {@inheritdoc}
     */
    public function createApplication()
    {
        $app = new Application(array(
            'env' => 'test',
        ));
        $app['session.test'] = true;

        return $app;
    }

}
