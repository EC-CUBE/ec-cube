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

use Eccube\Common\EccubeConfig;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

/**
 * ルーティングと firewall で管理画面のパスが食い違っていないかを検証するリスナ.
 *
 * 管理画面のルートのパスはルーティングのキャッシュを生成した時点の ECCUBE_ADMIN_ROUTE で固まるが,
 * firewall・access_control はリクエストのたびに ECCUBE_ADMIN_ROUTE を読む.
 * キャッシュの生成後に値が変わると, ルーティングと firewall が別のパスを指すため,
 * ルーティングに残った古いパスへのリクエストはコントローラへ渡さず 503 を返す.
 */
class AdminRouteConsistencyListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly EccubeConfig $eccubeConfig,
    ) {
    }

    #[\Override]
    public static function getSubscribedEvents(): array
    {
        // RouterListener(priority 32)の後, firewall(priority 8)の前に実行する.
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 30],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        try {
            $generated = $this->router->generate('admin_homepage');
        } catch (RouteNotFoundException) {
            return;
        }

        // generate() はベース URL を含むため, パス情報と比較できるよう取り除く
        $baseUrl = $this->router->getContext()->getBaseUrl();
        $routedPrefix = '' !== $baseUrl && str_starts_with($generated, $baseUrl)
            ? substr($generated, strlen($baseUrl))
            : $generated;
        $configuredPrefix = '/'.$this->eccubeConfig->get('eccube_admin_route').'/';

        if ($routedPrefix === $configuredPrefix) {
            return;
        }

        // ルーティングと firewall はデコードしたパスで照合するため, 同じくデコードしてから比較する
        if (!str_starts_with(rawurldecode($event->getRequest()->getPathInfo()), $routedPrefix)) {
            return;
        }

        log_critical('ルーティングと設定値で管理画面のパスが一致しません. bin/console eccube:cache:build でキャッシュを再生成してください.', [
            'routing' => $routedPrefix,
            'ECCUBE_ADMIN_ROUTE' => $configuredPrefix,
        ]);

        throw new ServiceUnavailableHttpException();
    }
}
