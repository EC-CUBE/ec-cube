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
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class [code]Event
{

    /** @var  \Eccube\Application $app */
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function onAppBefore()
    {
        error_log('onAppBefore');
    }

    public function onAppAfter()
    {
        error_log('onAppAfter');
    }

    public function onControllerBefore()
    {
        error_log('onControllerBefore');
    }

    public function onControllerAfter()
    {
        error_log('onControllerAfter');
    }

    public function onControllerFinish()
    {
        error_log('onControllerFinish');
    }

    public function onRenderBefore(FilterResponseEvent $event)
    {
        error_log('onRenderBefore');
    }

}
