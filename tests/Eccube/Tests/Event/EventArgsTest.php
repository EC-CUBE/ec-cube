<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
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

use Eccube\Event\EventArgs;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EventArgsrTest extends EccubeTestCase
{
    public function testRequest()
    {
        $event = new EventArgs(array());
        $this->assertNull($event->getRequest());

        $request = new Request();
        $event->setRequest($request);

        $this->assertNotNull($event->getRequest());
    }

    public function testResponse()
    {
        $event = new EventArgs(array());
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
