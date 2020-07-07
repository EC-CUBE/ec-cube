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
use Symfony\Component\EventDispatcher\EventDispatcher;

class TemplateEventListenerTest extends AbstractWebTestCase
{
    public function test()
    {
        $calledEvents = [];
        /** @var EventDispatcher $eventDispatcher */
        $eventDispatcher = self::$container->get('event_dispatcher');
        $listener = function ($event) use (&$calledEvents) {
            /* @var TemplateEvent $event */
            $calledEvents[] = $event->getView();
        };
        $eventDispatcher->addListener('index.twig', $listener);
        $eventDispatcher->addListener('Block/login.twig', $listener);

        $this->client->request('GET', $this->generateUrl('homepage'));
        self::assertEquals([
            'index.twig',
            'Block/login.twig',
        ], $calledEvents);
    }
}
