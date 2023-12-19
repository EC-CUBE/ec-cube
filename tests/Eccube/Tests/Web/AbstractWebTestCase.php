<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Web;

use Eccube\Entity\Customer;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractWebTestCase extends EccubeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // TODO このタイミングだとセッションは生成できないのでいったんコメントアウト
        // $this->createSession();
    }

    protected function tearDown(): void
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
     *
     * @return Symfony\Component\HttpKernel\Client
     *
     * @see EccubeTestCase::getCsrfToken()
     */
    public function loginTo(UserInterface $User)
    {
        $firewallContext = $User instanceof Customer ? 'customer' : 'admin';
        $this->client->loginUser($User, $firewallContext);

        return $this->client;
    }

    public function createSession()
    {
        // セッションが途中できれてしまうような事象が発生するため
        // https://github.com/symfony/symfony/issues/13450#issuecomment-353745790
        $session = $this->client->getContainer()->get('session');
        $session->set('dummy', 'dummy');
        $session->save();
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}
