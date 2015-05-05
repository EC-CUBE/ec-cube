<?php

namespace Eccube\Tests\Web;

use Silex\WebTestCase;
use Eccube\Application;

abstract class AbstractWebTestCase extends WebTestCase
{

    protected $client = null;
    static protected $server = null;

    public function setUp()
    {
        parent::setUp();

        if ($this->client == null) {
            if (self::$server == null) {
                self::$server = static::createClient();
            }
            $this->client = self::$server;
        }
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->app['orm.em']->getConnection()->close();
        $this->app = null;
        $this->client = null;
    }

    public static function tearDownAfterClass()
    {
        self::$server = NULL;
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
