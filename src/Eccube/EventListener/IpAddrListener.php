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
use Eccube\Request\Context;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpFoundation\IpUtils;

class IpAddrListener implements EventSubscriberInterface
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var Context
     */
    protected $requestContext;

    public function __construct(EccubeConfig $eccubeConfig, Context $requestContext)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->requestContext = $requestContext;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if (!$this->requestContext->isAdmin()) {

            // 許可リストを取得
            $allowFrontHosts = $this->eccubeConfig['eccube_front_allow_hosts'];

            foreach ($allowFrontHosts as $host) {
                // IPアドレス許可リスト範囲になければ拒否
                if (!IpUtils::checkIp($event->getRequest()->getClientIp(), $host)) {
                    throw new AccessDeniedHttpException();
                }
            }

            // 拒否リストを取得
            $denyFrontHosts =  $this->eccubeConfig['eccube_front_deny_hosts'];

            foreach ($denyFrontHosts as $host) {
                // IPアドレス拒否リスト範囲にあれば拒否
                if (IpUtils::checkIp($event->getRequest()->getClientIp(), $host)) {
                    throw new AccessDeniedHttpException();
                }
            }

            return;
        }

        // IPアドレス許可リストを確認
        $allowAdminHosts = $this->eccubeConfig['eccube_admin_allow_hosts'];

        foreach ($allowAdminHosts as $host) {
            // IPアドレス許可リスト範囲になければ拒否
            if (!IpUtils::checkIp($event->getRequest()->getClientIp(), $host)) {
                throw new AccessDeniedHttpException();
            }
        }

        // IPアドレス拒否リストを確認
        $denyAdminHosts = $this->eccubeConfig['eccube_admin_deny_hosts'];
        foreach ($denyAdminHosts as $host) {
            // IPアドレス拒否リスト範囲にあれば拒否
            if (IpUtils::checkIp($event->getRequest()->getClientIp(), $host)) {
                throw new AccessDeniedHttpException();
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => ['onKernelRequest', 512],
        ];
    }
}
