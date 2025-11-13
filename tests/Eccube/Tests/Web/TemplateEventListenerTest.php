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

namespace Eccube\Tests\Web;

use Eccube\Event\TemplateEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class TemplateEventListenerTest extends AbstractWebTestCase
{
    public function test()
    {
        $calledEvents = [];
        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = static::getContainer()->get(EventDispatcherInterface::class);
        $listener = function ($event) use (&$calledEvents) {
            /* @var TemplateEvent $event */
            $calledEvents[] = $event->getView();
        };
        $eventDispatcher->addListener('index.twig', $listener);
        $eventDispatcher->addListener('Block/login.twig', $listener);

        $this->client->request(Request::METHOD_GET, $this->generateUrl('homepage'));
        $this->assertSame([
            'index.twig',
            'Block/login.twig',
        ], $calledEvents);
    }
}
