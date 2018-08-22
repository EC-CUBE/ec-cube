<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\EventListener;

use Eccube\Common\EccubeConfig;
use Eccube\Request\Context;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
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

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $allowHosts = $this->eccubeConfig['eccube_admin_allow_hosts'];

        if (empty($allowHosts)) {
            return;
        }

        if ($this->requestContext->isAdmin()) {
            if (array_search($event->getRequest()->getClientIp(), $allowHosts) === false) {
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
