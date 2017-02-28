<?php

/*
 * This file is part of the [code]
 *
 * Copyright (C) [year] [author]
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\[code];

use Eccube\Application;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;

class [code]Event
{

    /** @var  \Eccube\Application $app */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function onAppRequest(GetResponseEvent $event)
    {
        error_log('onAppRequest');
    }

    public function onAppController(FilterControllerEvent $event)
    {
        error_log('onAppController');
    }

    public function onAppResponse(FilterResponseEvent $event)
    {
        error_log('onAppResponse');
    }

    public function onAppException(GetResponseForExceptionEvent $event)
    {
        error_log('onAppException');
    }

    public function onAppTerminate(PostResponseEvent $event)
    {
        error_log('onAppTerminate');
    }

    public function onFrontRequest(GetResponseEvent $event)
    {
        error_log('onFrontRequest');
    }

    public function onFrontController(FilterControllerEvent $event)
    {
        error_log('onFrontController');
    }

    public function onFrontResponse(FilterResponseEvent $event)
    {
        error_log('onFrontResponse');
    }

    public function onFrontException(GetResponseForExceptionEvent $event)
    {
        error_log('onFrontException');
    }

    public function onFrontTerminate(PostResponseEvent $event)
    {
        error_log('onFrontTerminate');
    }

    public function onAdminRequest(GetResponseEvent $event)
    {
        error_log('onAdminRequest');
    }

    public function onAdminController(FilterControllerEvent $event)
    {
        error_log('onAdminController');
    }

    public function onAdminResponse(FilterResponseEvent $event)
    {
        error_log('onAdminResponse');
    }

    public function onAdminException(GetResponseForExceptionEvent $event)
    {
        error_log('onAdminException');
    }

    public function onAdminTerminate(PostResponseEvent $event)
    {
        error_log('onAdminTerminate');
    }

    public function onRouteRequest(GetResponseEvent $event)
    {
        error_log('onRouteRequest');
    }

    public function onRouteController(FilterControllerEvent $event)
    {
        error_log('onRouteController');
    }

    public function onRouteResponse(FilterResponseEvent $event)
    {
        error_log('onRouteResponse');
    }

    public function onRouteException(GetResponseForExceptionEvent $event)
    {
        error_log('onRouteException');
    }

    public function onRouteTerminate(PostResponseEvent $event)
    {
        error_log('onRouteTerminate');
    }

}
