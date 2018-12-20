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

use Eccube\Service\SystemService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * メンテナンス管理制御のためのListener
 */
class MaintenanceListener implements EventSubscriberInterface
{

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var SystemService
     */
    protected $systemService;

    /**
     * MaintenanceListener constructor.
     *
     * @param SessionInterface $session
     * @param \Twig_Environment $twig
     * @param SystemService $systemService
     */
    public function __construct(SessionInterface $session, \Twig_Environment $twig, SystemService $systemService)
    {
        $this->session = $session;
        $this->twig = $twig;
        $this->systemService = $systemService;
    }


    /**
     * Kernel request listener callback.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $isMaintenance = $this->systemService->isMaintenanceMode();
        if (!$isMaintenance) {
            return;
        }

        // メンテナンス中であればメンテナンス画面を表示させるが、管理者としてログインされていればフロント画面を表示させる
        $is_admin = $this->session->has('_security_admin');
        if (!$is_admin) {
            $request = $event->getRequest();

            $pathInfo = \rawurldecode($request->getPathInfo());
            $adminPath = env('ECCUBE_ADMIN_ROUTE', 'admin');
            $adminPath = '/'.\trim($adminPath, '/').'/';
            if (\strpos($pathInfo, $adminPath) !== 0) {
                $twig = $this->twig->render('maintenance.twig');
                $response = new Response($twig, Response::HTTP_SERVICE_UNAVAILABLE);
                $event->setResponse($response);
                $event->stopPropagation();
            }

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
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
