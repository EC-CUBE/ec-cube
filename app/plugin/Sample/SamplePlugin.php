<?php

namespace Plugin\Sample;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Eccube\Event\RenderEvent;

class SamplePlugin implements EventSubscriberInterface
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public static function getSubscribedEvents() {
        return array(
            'eccube.event.controller.cart.before' => array(
                array('onCartIndexBefore', 10),
            ),
            'eccube.event.controller.cart.after' => array(
                array('onCartIndexAfter', 10),
            ),
            'eccube.event.controller.cart.finish' => array(
                array('onCartIndexFinish', 10),
            ),
            'eccube.event.render.cart.before' => array(
                array('onCartRenderBefore', 10),
            ),
        );
    }

    public function onCartIndexBefore()
    {
        echo 'Called method:: onCartIndexBefore()<br />';
    }

    public function onCartIndexAfter()
    {
        echo 'Called method:: onCartIndexAfter()<br />';
    }

    public function onCartIndexFinish()
    {
        echo 'Called method:: onCartIndexFinish()<br />';
    }

    public function onCartRenderBefore(RenderEvent $event)
    {
        $event->replace(array('アイスクリーム'), array('３倍アイスクリーム'));
    }
}