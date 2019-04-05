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

use Eccube\Event\EventArgs;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EventArgsrTest extends EccubeTestCase
{
    public function testRequest()
    {
        $event = new EventArgs([]);
        $this->assertNull($event->getRequest());

        $request = new Request();
        $event->setRequest($request);

        $this->assertNotNull($event->getRequest());
    }

    public function testResponse()
    {
        $event = new EventArgs([]);
        $this->assertNull($event->getResponse());
        $this->assertFalse($event->hasResponse());

        // 通常のレスポンスの検証
        $response = new Response();
        $event->setResponse($response);

        $this->assertNotNull($event->getResponse());
        $this->assertTrue($event->hasResponse());

        // リダイレクトレスポンスの検証
        $response = new RedirectResponse('http://www.ec-cube.net/');
        $event->setResponse($response);

        $this->assertNotNull($event->getResponse());
        $this->assertTrue($event->hasResponse());
    }
}
