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
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractWebTestCase extends EccubeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * @deprecated AbstractWebTestCase::loginTo() を使用してください.
     *
     * @param mixed|null $user
     */
    public function logIn(mixed $user = null)
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
     * @see EccubeTestCase::getCsrfToken()
     */
    public function loginTo(UserInterface $User): KernelBrowser|AbstractBrowser
    {
        $firewallContext = $User instanceof Customer ? 'customer' : 'admin';
        $this->client->loginUser($User, $firewallContext);

        return $this->client;
    }

    /**
     * https://github.com/symfony/symfony/discussions/46961
     */
    public function createSession(KernelBrowser $client): Session
    {
        $cookie = $client->getCookieJar()->get('MOCKSESSID');

        // create a new session object
        $container = static::getContainer();
        // SymfonyのFrameworkBundleが内部で登録するサービスだが、
        // クラス名のエイリアスが標準では存在しないため、文字列サービスIDのまま使用
        $serviceId = 'session.factory';
        $session = $container->get($serviceId)->createSession();

        if ($cookie) {
            // get the session id from the session cookie if it exists
            $session->setId($cookie->getValue());
            $session->start();
            $session->save();
        } else {
            // or create a new session id and a session cookie
            $session->start();
            $session->save();

            $sessionCookie = new Cookie(
                $session->getName(),
                $session->getId(),
                null,
                null,
                'localhost',
            );
            $client->getCookieJar()->set($sessionCookie);
        }

        return $session;
    }
}
