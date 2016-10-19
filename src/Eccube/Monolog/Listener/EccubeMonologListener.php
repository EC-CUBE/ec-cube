<?php

namespace Eccube\Monolog\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Log request,controller,terminate and exceptions.
 */
class EccubeMonologListener implements EventSubscriberInterface
{

    /**
     * Logs master requests on event KernelEvents::Request.
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->logRequest($event->getRequest());
    }

    /**
     * Logs master requests on event KernelEvents::CONTROLLER.
     *
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->logController($event->getRequest());
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->logResponse($event->getRequest(), $event->getResponse());
    }

    /**
     * Logs terminate on event KernelEvents::TERMINATE.
     *
     * @param PostResponseEvent $event
     */
    public function onKernelTerminate(PostResponseEvent $event)
    {
        $this->logTerminate($event->getRequest(), $event->getResponse());
    }

    /**
     * Logs uncaught exceptions on event KernelEvents::EXCEPTION.
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $this->logException($event->getException());
    }

    /**
     * Logs a request
     *
     * @param Request $request
     */
    protected function logRequest(Request $request)
    {
        // このイベントのタイミングでは、ルーティングは確定していない.
        \EccubeLog::info('PRCESS START');
    }

    /**
     * Logs a controller
     *
     * @param Request $request
     */
    protected function logController(Request $request)
    {
        $route = $request->attributes->get('_route');
        \EccubeLog::info('LOGIC START', array($route));
    }

    /**
     * Logs a response.
     *
     * @param Response $response
     */
    protected function logResponse(Request $request, Response $response)
    {
        $route = $request->attributes->get('_route');
        \EccubeLog::info('LOGIC END', array($route));
    }

    protected function logTerminate(Request $request, Response $response)
    {
        $route = $request->attributes->get('_route');
        \EccubeLog::info('PRCESS END', array($route));
    }

    /**
     * Logs an exception.
     *
     * @param \Exception $e
     */
    protected function logException(\Exception $e)
    {
        if ($e instanceof HttpExceptionInterface && $e->getStatusCode() < 500) {
            \EccubeLog::info($e->getMessage(), array($e->getStatusCode()));

        } else {
            $message = sprintf(
                '%s: %s (uncaught exception) at %s line %s',
                get_class($e),
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            );
            \EccubeLog::error($message, array('exception' => $e));
        }
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array('onKernelRequest', 500),
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
}
