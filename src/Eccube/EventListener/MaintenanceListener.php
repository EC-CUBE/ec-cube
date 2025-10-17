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

use Eccube\Entity\Member;
use Eccube\Request\Context;
use Eccube\Service\SystemService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class MaintenanceListener implements EventSubscriberInterface
{
    /** @var Context */
    protected $requestContext;

    /** @var SystemService */
    protected $systemService;

    public function __construct(Context $requestContext, SystemService $systemService)
    {
        $this->requestContext = $requestContext;
        $this->systemService = $systemService;
    }

    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onResponse'],
        ];
    }

    /**
     * @param ResponseEvent $event
     *
     * @return void
     */
    public function onResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        if (!$this->systemService->isMaintenanceMode()) {
            $response->headers->clearCookie(SystemService::MAINTENANCE_TOKEN_KEY);

            return;
        }

        $user = $this->requestContext->getCurrentUser();
        if ($user instanceof Member && $this->requestContext->isAdmin()) {
            $cookie = (new Cookie(
                SystemService::MAINTENANCE_TOKEN_KEY,
                $this->systemService->getMaintenanceToken()
            ))->withSecure(true);
            $response->headers->setCookie($cookie);
        }
    }
}
