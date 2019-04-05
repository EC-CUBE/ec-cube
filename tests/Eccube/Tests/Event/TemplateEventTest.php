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

namespace Eccube\Tests\Event;

use Eccube\Event\TemplateEvent;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TemplateEventTest
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
        $this->assertSame([], $templateEvent->getParameters());

        $parameter = ['id' => 1];

        // set parameter
        $templateEvent->setParameters($parameter);

        $this->assertSame($parameter, $templateEvent->getParameters());
    }
}
