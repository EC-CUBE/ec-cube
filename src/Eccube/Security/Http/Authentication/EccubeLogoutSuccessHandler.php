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

namespace Eccube\Security\Http\Authentication;

use Eccube\Request\Context;
use Eccube\Service\SystemService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;

class EccubeLogoutSuccessHandler implements EventSubscriberInterface
{
    /** @var Context */
    protected $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function onLogout(LogoutEvent $event)
    {
        if ($this->context->isAdmin()) {
            $response = $event->getResponse();
            $response->headers->clearCookie(SystemService::MAINTENANCE_TOKEN_KEY);
        }
    }

    public static function getSubscribedEvents()
    {
        return [LogoutEvent::class => 'onLogout'];
    }
}
