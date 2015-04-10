<?php

namespace Plugin\Sample;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SamplePlugin implements EventSubscriberInterface
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public static function getSubscribedEvents() {
        return array(
            'eccube.event.controller.cart.index.before' => array(
                array('onCartIndexBefore', 10),
            ),
            'eccube.event.controller.cart.index.after' => array(
                array('onCartIndexAfter', 10),
            ),
            'eccube.event.controller.cart.index.finish' => array(
                array('onCartIndexFinish', 10),
            ),
        );
    }

    public function onCartIndexBefore()
    {
        echo 'Called method:: onCartIndexBefore()<br />';
    }

    public function onCartIndexAfter()
    {
        echo 'Called method:: onCartIndexBefore()<br />';
    }

    public function onCartIndexFinish()
    {
        echo 'Called method:: onCartIndexFinish()<br />';
    }
}