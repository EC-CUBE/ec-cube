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

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Check to ForwardOnly annotation.
 *
 * @author Kentaro Ohkouchi
 */
class ForwardOnlyListener implements EventSubscriberInterface
{
    /**
     * Kernel Controller listener callback.
     *
     * @param FilterControllerEvent $event
     */
    public function onController(FilterControllerEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if (!is_array($controller = $event->getController())) {
            return;
        }

        $request = $event->getRequest();
        $attributes = $request->attributes;

        $forwardOnly = $attributes->has('_forward_only');

        if ($forwardOnly) {
            $message = sprintf('%s is Forward Only', $attributes->get('_controller'));
            throw new AccessDeniedHttpException($message);
        }
    }

    /**
     * Return the events to subscribe to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onController',
        ];
    }
}
