<?php

/*
 * This file is part of the Silex framework.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Eccube\Tests\Application;

use Eccube\Entity\Customer;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

class SecurityTraitTest extends EccubeTestCase
{
    public function testUser()
    {
        self::assertNull($this->app->user());
        self::assertNull($this->app['user']);
    }

    public function testUserWithCustomerLogin()
    {
        $user = $this->createCustomer();
        $this->loginTo($user);

        self::assertSame($user, $this->app->user());
        self::assertSame($user, $this->app['user']);
    }

    public function testUserWithMemberLogin()
    {
        $user = $this->createCustomer();
        $this->loginTo($user);

        self::assertSame($user, $this->app->user());
        self::assertSame($user, $this->app['user']);
    }

    protected function loginTo(UserInterface $User)
    {
        $firewall = 'admin';
        $role = array('ROLE_ADMIN');
        if ($User instanceof Customer) {
            $firewall = 'customer';
            $role = array('ROLE_USER');
        }
        $token = new UsernamePasswordToken($User, null, $firewall, $role);

        $this->app['security.token_storage']->setToken($token);
        $this->app['session']->set('_security_'.$firewall, serialize($token));
        $this->app['session']->save();

        $cookie = new Cookie($this->app['session']->getName(), $this->app['session']->getId());
        $client = $this->createClient();
        $client->getCookieJar()->set($cookie);

        return $client;
    }
}
