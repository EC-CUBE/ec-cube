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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class MaintenanceListener implements EventSubscriberInterface
{
    private const SESSION_KEY = '_security_admin';

    /** @var Context */
    private $context;

    /** @var SystemService */
    private $systemService;

    public function __construct(Context $context, SystemService $systemService)
    {
        $this->context = $context;
        $this->systemService = $systemService;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onRequest',
        ];
    }

    public function onRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if ($this->context->isAdmin() || !$this->systemService->isMaintenanceMode()) {
            return;
        }

        $request = $event->getRequest();
        if ($this->isAdminAuthenticated($request)) {
            return;
        }

        $locale = \env('ECCUBE_LOCALE');
        $templateCode = \env('ECCUBE_TEMPLATE_CODE');
        $baseUrl = htmlspecialchars(\rawurldecode($request->getBaseUrl()), ENT_QUOTES);

        \ob_start();
        require __DIR__ . '/../../../maintenance.php';
        $response = new Response(\ob_get_clean(), 503);
        $event->setResponse($response);
    }

    private function isAdminAuthenticated(Request $request): bool
    {
        $session = $request->hasPreviousSession() ? $request->getSession() : null;

        if ($session === null || ($serializedToken = $session->get(self::SESSION_KEY)) === null) {
            return false;
        }

        $unserializedToken = $this->safelyUnserialize($serializedToken);
        $user = ($unserializedToken instanceof TokenInterface)
            ? $unserializedToken->getUser()
            : null;

        return $user instanceof Member;
    }

    private function safelyUnserialize($serializedToken)
    {
        $e = $token = null;
        $prevUnserializeHandler = \ini_set('unserialize_callback_func', __CLASS__ . '::handleUnserializeCallback');
        $prevErrorHandler = \set_error_handler(function ($type, $msg, $file, $line, $context = []) use (&$prevErrorHandler) {
            if (__FILE__ === $file) {
                throw new \UnexpectedValueException($msg, 0x37313bc);
            }

            return $prevErrorHandler ? $prevErrorHandler($type, $msg, $file, $line, $context) : false;
        });

        try {
            $token = \unserialize($serializedToken);
        } catch (\Error $e) {
        } catch (\Exception $e) {
        }
        \restore_error_handler();
        \ini_set('unserialize_callback_func', $prevUnserializeHandler);
        if ($e) {
            if (!$e instanceof \UnexpectedValueException || 0x37313bc !== $e->getCode()) {
                throw $e;
            }
        }

        return $token;
    }

    /**
     * @internal
     */
    public static function handleUnserializeCallback($class)
    {
        throw new \UnexpectedValueException('Class not found: ' . $class, 0x37313bc);
    }
}
