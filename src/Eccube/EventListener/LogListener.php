<?php

namespace Eccube\EventListener;

use Psr\Log\LoggerAwareInterface;
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
 *
 * @package Eccube\EventListener
 */
class LogListener implements EventSubscriberInterface, LoggerAwareInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->setLogger($logger);
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequestEarly(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->logger->info('INIT');
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
        $this->logger->info('PROCESS START', array($route));
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
        $this->logger->info('LOGIC START', array($route));
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
        $this->logger->info('LOGIC END', array($route));
    }

    /**
     * @param PostResponseEvent $event
     */
    public function onKernelTerminate(PostResponseEvent $event)
    {
        $route = $this->getRoute($event->getRequest());
        $this->logger->info('PROCESS END', array($route));
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $e = $event->getException();
        if ($e instanceof HttpExceptionInterface && $e->getStatusCode() < 500) {
            $this->logger->info($e->getMessage(), array($e->getStatusCode()));

        } else {
            $message = sprintf(
                '%s: %s (uncaught exception) at %s line %s',
                get_class($e),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            );
            $this->logger->error($message, array('exception' => $e));
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(

            KernelEvents::REQUEST => array(
                // Application::initRenderingで、フロント/管理画面の判定が行われた後に実行
                array('onKernelRequestEarly', 500),
                // SecurityServiceProviderで、認証処理が完了した後に実行.
                array('onKernelRequest', 6),
            ),
            KernelEvents::RESPONSE => array('onKernelResponse', 0),
            KernelEvents::CONTROLLER => array('onKernelController', 0),
            KernelEvents::TERMINATE => array('onKernelTerminate', 0),
            /*
             * Priority -4 is used to come after those from SecurityServiceProvider (0)
             * but before the error handlers added with Silex\Application::error (defaults to -8)
             */
            KernelEvents::EXCEPTION => array('onKernelException', -4),
        );
    }

    /**
     * ルーティング名を取得する.
     *
     * @param $request
     * @return string
     */
    private function getRoute($request)
    {
        return $request->attributes->get('_route');
    }
}
