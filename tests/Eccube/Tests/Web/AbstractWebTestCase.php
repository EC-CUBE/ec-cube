<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Tests\Web;

use Eccube\Tests\EccubeTestCase;
use Eccube\Tests\Mock\CsrfTokenMock;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractWebTestCase extends EccubeTestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    /**
     * @deprecated AbstractWebTestCase::loginTo() を使用してください.
     */
    public function logIn($user = null)
    {
        if (!is_object($user)) {
            $user = $this->createCustomer();
        }
        $this->loginTo($user);
        return $user;
    }

    /**
     * User をログインさせてHttpKernel\Client を返す.
     *
     * EccubeTestCase::getCsrfToken() を使用する場合は, この関数をコールする前に実行すること.
     *
     * @param UserInterface $User ログインさせる User
     * @return Symfony\Component\HttpKernel\Client
     * @see EccubeTestCase::getCsrfToken()
     */
    public function loginTo(UserInterface $User)
    {
        $firewall = 'admin';
        $role = array('ROLE_ADMIN');
        if ($User instanceof \Eccube\Entity\Customer) {
            $firewall = 'customer';
            $role = array('ROLE_USER');
        }
        $token = new UsernamePasswordToken($User, null, $firewall, $role);

        $session = $this->container->get('session');

        $session->set('_security_' . $firewall, serialize($token));
        $session->save();
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

        return $this->client;
    }
}
