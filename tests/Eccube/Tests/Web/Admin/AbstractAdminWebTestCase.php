<?php

namespace Eccube\Tests\Web\Admin;

use Silex\WebTestCase;
use Eccube\Application;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

abstract class AbstractAdminWebTestCase extends WebTestCase
{

    public $client = null;

    public function setUp()
    {
        parent::setUp();
        $this->client = static::createClient();
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

    public function logIn()
    {

        $firewall = 'admin';

        $user = $this->app['eccube.repository.member']
            ->findOneBy(array(
                'login_id' => 'admin',
            ));

        $token = new UsernamePasswordToken($user, null, $firewall, array('ROLE_ADMIN'));

        $this->app['session']->set('_security_' . $firewall, serialize($token));
        $this->app['session']->save();

        $cookie = new Cookie($this->app['session']->getName(), $this->app['session']->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    public function tearDown()
    {
        $this->app['orm.em']->getConnection()->close();
        parent::tearDown();
    }
}
