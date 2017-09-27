<?php

namespace Eccube\EventListener;

use Eccube\Application;
use Eccube\Annotation\ForwardOnly;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Check to ForwardOnly annotation.
 *
 * @author Kentaro Ohkouchi
 */
class ForwardOnlyListener implements EventSubscriberInterface
{
    private $app;

    /**
     * Constructor function.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }


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
        list($controller, $method) = $event->getController();
        $refClass = new \ReflectionClass($controller);
        $refMethod = $refClass->getMethod($method);
        $anno = $this->app['eccube.di.annotation_reader']->getMethodAnnotation($refMethod, ForwardOnly::class);
        if ($anno) {
            $log = $refClass->getName().'::'.$refMethod->getName().' is Forward Only';
            $this->app->log($log, array(), Logger::ERROR);
            log_error($log);
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException($log);
        }
    }

    /**
     * Return the events to subscribe to.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => 'onController',
        );
    }
}
