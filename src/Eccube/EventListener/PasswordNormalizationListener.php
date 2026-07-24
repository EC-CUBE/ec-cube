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

namespace Eccube\EventListener;

use Eccube\Util\PasswordNormalizer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * ログイン照合時に送信パスワードを NFKC 正規化するリスナ.
 *
 * NIST SP 800-63B-4 対応で, パスワード設定時に NFKC 正規化して保存している(PasswordNormalizer 参照).
 * ログイン時にも同じ正規化を施さないと, Unicode パスワードの表記ゆれで照合に失敗するため,
 * firewall(form_login)がパラメータを読む前に request 上の値を正規化しておく.
 *
 * 既存の ASCII パスワードは正規化しても不変(no-op)のため影響しない.
 */
class PasswordNormalizationListener implements EventSubscriberInterface
{
    /**
     * ログインの check_path ルート名 => password_parameter のマッピング.
     * security.yaml の firewalls.*.form_login と対応する.
     */
    private const LOGIN_PASSWORD_PARAMETERS = [
        'admin_login' => 'password',
        'mypage_login' => 'login_pass',
    ];

    #[\Override]
    public static function getSubscribedEvents(): array
    {
        // RouterListener(priority 32)の後, firewall(priority 8)の前に実行する.
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 16],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!$request->isMethod('POST')) {
            return;
        }

        $route = $request->attributes->get('_route');
        if (!isset(self::LOGIN_PASSWORD_PARAMETERS[$route])) {
            return;
        }

        $parameter = self::LOGIN_PASSWORD_PARAMETERS[$route];
        $password = $request->request->get($parameter);

        if (is_string($password) && $password !== '') {
            $request->request->set($parameter, PasswordNormalizer::normalize($password));
        }
    }
}
