<?php

declare(strict_types=1);

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

namespace Eccube\Tests\EventListener;

use Eccube\EventListener\SecurityListener;
use Eccube\Tests\Web\AbstractWebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

final class SecurityListenerTest extends AbstractWebTestCase
{
    /**
     * ステートフルなリクエスト(フロントのログインフォーム)では,
     * login_memory の値がセッションに保存されること.
     */
    public function testOnAuthenticationFailureStoresLoginMemory(): void
    {
        $request = Request::create($this->generateUrl('mypage_login'), Request::METHOD_POST, ['login_memory' => '1']);
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $this->dispatchLoginFailureEvent($request);

        $this->assertTrue($session->get('_security.login_memory'));
    }

    /**
     * ステートレスなファイアウォール(API 等)からの認証失敗では, セッションを使用しないこと.
     *
     * セッションを使用すると UnexpectedSessionUsageException が発生し,
     * 401 を返すべきところが 500 になる.
     *
     * @see https://github.com/EC-CUBE/ec-cube/pull/6760
     */
    public function testOnAuthenticationFailureWithStatelessRequest(): void
    {
        $request = Request::create('/api', Request::METHOD_POST);
        $request->attributes->set('_stateless', true);
        $session = new Session(new MockArraySessionStorage());
        $request->setSession($session);

        $this->dispatchLoginFailureEvent($request);

        $this->assertFalse($session->isStarted());
        $this->assertNull($session->get('_security.login_memory'));
    }

    /**
     * セッションを持たないリクエストでは, SessionNotFoundException を発生させないこと.
     */
    public function testOnAuthenticationFailureWithoutSession(): void
    {
        $request = Request::create('/api', Request::METHOD_POST);

        $this->dispatchLoginFailureEvent($request);

        $this->assertFalse($request->hasSession());
    }

    private function dispatchLoginFailureEvent(Request $request): void
    {
        $requestStack = static::getContainer()->get(RequestStack::class);
        $requestStack->push($request);

        $event = new LoginFailureEvent(
            new AuthenticationException(),
            $this->createStub(AuthenticatorInterface::class),
            $request,
            null,
            'customer'
        );

        try {
            static::getContainer()->get(SecurityListener::class)->onAuthenticationFailure($event);
        } finally {
            $requestStack->pop();
        }
    }
}
