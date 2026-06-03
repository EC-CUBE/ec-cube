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

use Eccube\Common\EccubeConfig;
use Eccube\Entity\LoginHistory;
use Eccube\Entity\Master\LoginHistoryStatus;
use Eccube\EventListener\LoginHistoryListener;
use Eccube\Tests\Web\AbstractWebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

final class LoginHistoryListenerTest extends AbstractWebTestCase
{
    public function activeLogin()
    {
        $this->client->request(
            Request::METHOD_POST, $this->generateUrl('admin_login'),
            [
                'login_id' => 'admin',
                'password' => 'password',
                '_csrf_token' => 'dummy',
            ]
        );

        $LoginHistory = $this->entityManager->getRepository(LoginHistory::class)
            ->findOneBy([
                'user_name' => 'admin',
                'Status' => LoginHistoryStatus::SUCCESS,
            ]);
        $this->assertInstanceOf(LoginHistory::class, $LoginHistory);

        // $LoginHistoryの比較だと、RECURSIONが発生するため、IDの有無で確認
        $this->assertNotNull($LoginHistory->getId());
    }

    public function testOnAuthenticationFailure()
    {
        $this->client->request(
            Request::METHOD_POST, $this->generateUrl('admin_login'),
            [
                'login_id' => 'admin',
                'password' => 'password2',
                '_csrf_token' => 'dummy',
            ]
        );

        $LoginHistory = $this->entityManager->getRepository(LoginHistory::class)
            ->findOneBy([
                'user_name' => 'admin',
                'Status' => LoginHistoryStatus::FAILURE,
            ]);

        $this->assertInstanceOf(LoginHistory::class, $LoginHistory);
    }

    /**
     * passport が null の場合（Bearer/AccessToken 系認証でトークン抽出前に失敗した場合など）、
     * 例外を発生させず、ログイン履歴も記録しないこと.
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/6783
     */
    public function testOnAuthenticationFailureWithNullPassport()
    {
        $countBefore = $this->countFailureHistories();

        $this->dispatchLoginFailureEvent(null);

        $this->assertSame($countBefore, $this->countFailureHistories());
    }

    /**
     * passport が UserBadge を持たない場合、ユーザーを特定できないため、
     * 例外を発生させず、ログイン履歴も記録しないこと.
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/6783
     */
    public function testOnAuthenticationFailureWithoutUserBadge()
    {
        $countBefore = $this->countFailureHistories();

        $passport = $this->createMock(Passport::class);
        $passport->method('hasBadge')->willReturn(false);

        $this->dispatchLoginFailureEvent($passport);

        $this->assertSame($countBefore, $this->countFailureHistories());
    }

    private function dispatchLoginFailureEvent(?Passport $passport): void
    {
        $adminRoute = static::getContainer()->get(EccubeConfig::class)->get('eccube_admin_route');
        $request = Request::create(sprintf('/%s/login', $adminRoute));

        static::getContainer()->get(RequestStack::class)->push($request);

        $event = new LoginFailureEvent(
            new AuthenticationException(),
            $this->createStub(AuthenticatorInterface::class),
            $request,
            null,
            'admin',
            $passport
        );

        static::getContainer()->get(LoginHistoryListener::class)->onAuthenticationFailure($event);
    }

    private function countFailureHistories(): int
    {
        return count($this->entityManager->getRepository(LoginHistory::class)
            ->findBy(['Status' => LoginHistoryStatus::FAILURE]));
    }
}
