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

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * ログ出力リスナー
 */
class LogListener implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                // Application::initRenderingで、フロント/管理画面の判定が行われた後に実行
                ['onKernelRequestEarly', 500],
                // SecurityServiceProviderで、認証処理が完了した後に実行.
                ['onKernelRequest', 6],
            ],
            KernelEvents::RESPONSE => ['onKernelResponse', 0],
            KernelEvents::CONTROLLER => ['onKernelController', 0],
            KernelEvents::TERMINATE => ['onKernelTerminate', 0],
            /*
             * Priority -4 is used to come after those from SecurityServiceProvider (0)
             * but before the error handlers added with Silex\Application::error (defaults to -8)
             */
            KernelEvents::EXCEPTION => ['onKernelException', -4],
        ];
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequestEarly(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->logger->debug('INIT');
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $route = $this->getRoute($event->getRequest());
        $this->logger->debug('PROCESS START', [$route]);
    }

    /**
     * ルーティング名を取得する.
     *
     * @param $request
     *
     * @return string
     */
    private function getRoute($request)
    {
        return $request->attributes->get('_route');
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $route = $this->getRoute($event->getRequest());
        $this->logger->debug('LOGIC START', [$route]);
    }

    /**
     * @param FilterResponseEvent $event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $route = $this->getRoute($event->getRequest());
        $this->logger->debug('LOGIC END', [$route]);
    }

    /**
     * @param PostResponseEvent $event
     */
    public function onKernelTerminate(PostResponseEvent $event)
    {
        $route = $this->getRoute($event->getRequest());
        $this->logger->debug('PROCESS END', [$route]);
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $e = $event->getException();
        if ($e instanceof HttpExceptionInterface && $e->getStatusCode() < 500) {
            $this->logger->info($e->getMessage(), [$e->getStatusCode()]);
        } else {
            $message = sprintf(
                '%s: %s (uncaught exception) at %s line %s',
                get_class($e),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            );
            $this->logger->error($message, ['exception' => $e]);
        }
    }
}
