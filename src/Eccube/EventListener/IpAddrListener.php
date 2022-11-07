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
            // IPアドレス許可リスト範囲にあるかを確認（フロント画面）
            if ($this->checkIpAddrRange($event->getRequest()->getClientIp()) === false) {
                throw new AccessDeniedHttpException();
            }

            return;
        }

        // IPアドレス許可リストを確認
        $allowHosts = $this->eccubeConfig['eccube_admin_allow_hosts'];

        if (!empty($allowHosts) && array_search($event->getRequest()->getClientIp(), $allowHosts) === false) {
            throw new AccessDeniedHttpException();
        }

        // IPアドレス拒否リストを確認
        $denyHosts = $this->eccubeConfig['eccube_admin_deny_hosts'];

        if (array_search($event->getRequest()->getClientIp(), $denyHosts) !== false) {
            throw new AccessDeniedHttpException();
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => ['onKernelRequest', 512],
        ];
    }

    private function checkIpAddrRange($remoteIp)
    {
        //フロント画面のIPアドレス許可リストを確認
        $allowFrontHosts = ['132.111.56.0/24']; //$this->eccubeConfig['eccube_allow_hosts'];

        if (empty($allowFrontHosts)) {
            return false;
        }

        // 設定したリストの分、ビットマスクに該当するかを判断する
        foreach ($allowFrontHosts as $allowIp) {
            list($accept_ip, $mask) = explode('/', $allowIp);
            $accept_long = ip2long($accept_ip) >> (32 - $mask);
            $remote_long = ip2long($remoteIp) >> (32 - $mask);
            if ($accept_long !== $remote_long) {
                return true;
            }
        }

        return false;
    }
}
