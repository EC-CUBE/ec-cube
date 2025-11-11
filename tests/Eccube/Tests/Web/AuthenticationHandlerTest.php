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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class AuthenticationHandlerTest extends AbstractWebTestCase
{
    private ?Customer $Customer = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->Customer = $this->createCustomer();
    }

    public function testAuthenticationSuccessHandler()
    {
        $this->client->request(Request::METHOD_POST, $this->generateUrl('mypage_login'), [
            '_csrf_token' => 'dummy',
            '_target_path' => $this->generateUrl('shopping'),
            '_failure_path' => $this->generateUrl('shopping_login'),
            'login_email' => $this->Customer->getEmail(),
            'login_pass' => 'password',
        ]);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping', [], UrlGeneratorInterface::ABSOLUTE_PATH)));
    }

    public function testAuthenticationFailureHandler()
    {
        $this->client->request(Request::METHOD_POST, $this->generateUrl('mypage_login'), [
            '_csrf_token' => 'dummy',
            '_target_path' => $this->generateUrl('shopping'),
            '_failure_path' => $this->generateUrl('shopping_login'),
            'login_email' => $this->Customer->getEmail(),
            'login_pass' => 'foo',
        ]);
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('shopping_login', [], UrlGeneratorInterface::ABSOLUTE_PATH)));
    }

    public function testAuthenticationSuccessHandlerWithInvalidPath()
    {
        $this->client->request(Request::METHOD_POST, $this->generateUrl('mypage_login'), [
            '_csrf_token' => 'dummy',
            '_target_path' => 'http://example.com/bar',
            '_failure_path' => $this->generateUrl('shopping_login'),
            'login_email' => $this->Customer->getEmail(),
            'login_pass' => 'password',
        ]);

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('homepage', [], UrlGeneratorInterface::ABSOLUTE_PATH)), 'アプリケーション外部のURLが指定された場合は homepage へリダイレクトする');
    }

    public function testAuthenticationFailureHandlerWithInvalidPath()
    {
        $this->client->request(Request::METHOD_POST, $this->generateUrl('mypage_login'), [
            '_csrf_token' => 'dummy',
            '_target_path' => $this->generateUrl('shopping'),
            '_failure_path' => 'http://example.com/baz',
            'login_email' => $this->Customer->getEmail(),
            'login_pass' => 'quux',
        ]);

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('homepage', [], UrlGeneratorInterface::ABSOLUTE_PATH)), 'アプリケーション外部のURLが指定された場合は homepage へリダイレクトする');
    }
}
