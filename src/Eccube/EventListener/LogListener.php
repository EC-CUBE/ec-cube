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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
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
    #[\Override]
    public static function getSubscribedEvents(): array
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
     * @param RequestEvent $event
     *
     * @return void
     */
    public function onKernelRequestEarly(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $this->logger->debug('INIT');
    }

    /**
     * @param RequestEvent $event
     *
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $route = $this->getRoute($event->getRequest());
        $this->logger->debug('PROCESS START', [$route]);
    }

    /**
     * ルーティング名を取得する.
     *
     * @param Request $request
     *
     * @return string|null
     */
    private function getRoute($request): ?string
    {
        return $request->attributes->get('_route');
    }

    /**
     * @param ControllerEvent $event
     *
     * @return void
     */
    public function onKernelController(ControllerEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $route = $this->getRoute($event->getRequest());
        $this->logger->debug('LOGIC START', [$route]);
    }

    /**
     * @param ResponseEvent $event
     *
     * @return void
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $route = $this->getRoute($event->getRequest());
        $this->logger->debug('LOGIC END', [$route]);
    }

    /**
     * @param TerminateEvent $event
     *
     * @return void
     */
    public function onKernelTerminate(TerminateEvent $event): void
    {
        $route = $this->getRoute($event->getRequest());
        $this->logger->debug('PROCESS END', [$route]);
    }

    /**
     * @param ExceptionEvent $event
     *
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $e = $event->getThrowable();
        if ($e instanceof HttpExceptionInterface && $e->getStatusCode() < 500) {
            $this->logger->info($e->getMessage(), [$e->getStatusCode()]);
        } else {
            $message = sprintf(
                '%s: %s (uncaught exception) at %s line %s',
                $e::class,
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            );
            $this->logger->error($message, ['exception' => $e]);
        }
    }
}
