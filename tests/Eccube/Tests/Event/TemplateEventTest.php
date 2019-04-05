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

namespace Eccube\Tests\Event;

use Eccube\Event\TemplateEvent;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TemplateEventTest
 * @package Eccube\Tests\Event
 */
class TemplateEventTest extends EccubeTestCase
{
    /**
     * View test
     */
    public function testView()
    {
        $templateEvent = new TemplateEvent(null, null);
        $this->assertNull($templateEvent->getView());

        $view = 'this is a test';

        // set view
        $templateEvent->setView($view);

        $this->assertNotNull($templateEvent->getView());
    }

    /**
     * Response test
     */
    public function testResponse()
    {
        $event = new TemplateEvent(null, null);
        $this->assertNull($event->getResponse());

        // setResponse test
        $response = new Response();
        $event->setResponse($response);
        $this->assertNotNull($event->getResponse());

        // リダイレクトレスポンスの検証
        $response = new RedirectResponse('http://www.ec-cube.net/');
        $event->setResponse($response);

        $this->assertNotNull($event->getResponse());
    }

    /**
     * Source test
     */
    public function testSource()
    {
        $templateEvent = new TemplateEvent(null, null);
        $this->assertNull($templateEvent->getSource());

        $source = 'this is a test';

        // set source
        $templateEvent->setSource($source);

        $this->assertNotNull($templateEvent->getSource());
    }

    /**
     * Parameter test
     */
    public function testParameter()
    {
        $templateEvent = new TemplateEvent(null, null);
        $this->assertSame(array(), $templateEvent->getParameters());

        $parameter = array('id' => 1);

        // set parameter
        $templateEvent->setParameters($parameter);

        $this->assertSame($parameter, $templateEvent->getParameters());
    }
}
