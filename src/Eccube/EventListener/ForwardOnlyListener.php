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

use Doctrine\Common\Annotations\AnnotationReader;
use Eccube\Annotation\ForwardOnly;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Check to ForwardOnly annotation/attribute.
 *
 * @author Kentaro Ohkouchi
 */
class ForwardOnlyListener implements EventSubscriberInterface
{
    /**
     * Kernel Controller listener callback.
     */
    public function onController(ControllerEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $controller = $event->getController();
        if (!is_array($controller)) {
            return;
        }

        $request = $event->getRequest();

        // Check for ForwardOnly via PHP 8 Attribute
        $reflMethod = new \ReflectionMethod($controller[0], $controller[1]);
        $forwardOnly = !empty($reflMethod->getAttributes(ForwardOnly::class));

        // Fallback: check via doctrine/annotations
        if (!$forwardOnly) {
            $reader = new AnnotationReader();
            $anno = $reader->getMethodAnnotation($reflMethod, ForwardOnly::class);
            if ($anno) {
                trigger_deprecation('ec-cube/ec-cube', '4.3', 'Using @ForwardOnly annotation is deprecated, use #[ForwardOnly] attribute instead.');
                $forwardOnly = true;
            }
        }

        // Legacy: check request attribute (set by SensioFrameworkExtraBundle)
        if (!$forwardOnly) {
            $forwardOnly = $request->attributes->has('_forward_only');
        }

        if ($forwardOnly) {
            $message = sprintf('%s is Forward Only', $request->attributes->get('_controller'));
            throw new AccessDeniedHttpException($message);
        }
    }

    /**
     * Return the events to subscribe to.
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onController',
        ];
    }
}
