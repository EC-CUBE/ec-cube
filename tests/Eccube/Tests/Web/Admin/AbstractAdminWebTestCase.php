<?php

namespace Eccube\Tests\Web\Admin;

use Eccube\Tests\Web\AbstractWebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

abstract class AbstractAdminWebTestCase extends AbstractWebTestCase
{

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

}
