<?php

namespace Eccube\Tests\Plugin\Ext;

use Eccube\Tests\EccubeTestCase;

class EventTest extends EccubeTestCase
{
    public function testGetSubscribedEvent()
    {
        $this->assertEmpty(\Ext\Event::getSubscribedEvents());
    }

    public function testExample()
    {
        $hook = 'eccube.event.front.controller';
        $method = 'example';
        $event = new \Ext\Event($this->app);

        // リスナ登録.getSubscribedEventsがstaticのため, addListerで登録.
        $this->app['eccube.event.dispatcher']->addListener($hook, array($event, $method));

        // 画面アクセスし、フックポイントが実行されるかどうか確認する.
        $client = $this->createClient();
        $client->request(
            'GET',
            $this->app->path('entry')
        );

        // フックポイント内で、フックポイント名がechoされている
        $this->expectOutputString($hook);

        // リスナ解除
        $this->app['eccube.event.dispatcher']->removeListener($hook, array($event, $method));
    }
}
