<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Tests\Plugin\CommonHookPoint;

class HookPointEvent
{
    /** @var  \Eccube\Application $app */
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function onAppBefore()
    {
        echo 'eccube.event.app.before';
    }

    public function onAppBeforeWithArgument($event = null)
    {
        echo get_class($event);
    }

    public function onControllerHomepageBefore()
    {
        echo 'eccube.event.controller.homepage.before';
    }

    public function onControllerHomepageBeforeWithArgument($event = null)
    {
        echo get_class($event);
    }

    public function onControllerHomepageAfter()
    {
        echo 'eccube.event.controller.homepage.after';
    }

    public function onControllerHomepageAfterWithArgument($event = null)
    {
        echo get_class($event);
    }

    public function onAppAfter()
    {
        echo 'eccube.event.app.after';
    }

    public function onAppAfterWithArgument($event = null)
    {
        echo get_class($event);
    }

    public function onControllerHomepageFinish()
    {
        echo 'eccube.event.controller.homepage.finish';
    }

    public function onControllerHomepageFinishWithArgument($event = null)
    {
        echo get_class($event);
    }

    public function onAppBeforeRedirect($event = null)
    {
        $response = $this->app->redirect($this->app->url('entry'));
        $event->setResponse($response);
    }

    public function onControllerHomepageBeforeRedirect($event = null)
    {
        $response = $this->app->redirect($this->app->url('help_tradelaw'));
        $event->setResponse($response);
    }
}
