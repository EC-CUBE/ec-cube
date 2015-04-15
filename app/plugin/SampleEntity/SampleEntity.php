<?php

namespace Plugin\SampleEntity;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SampleEntity implements EventSubscriberInterface
{
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public static function getSubscribedEvents() {
        return array(
            'eccube.event.controller.help.tradelaw.before' => array(
                array('onHelpTradelawBefore', 10),
            ),
        );
    }

    public function onHelpTradelawBefore()
    {
        $extend = $this->app['orm.em']->getRepository('Plugin\SampleEntity\Entity\Extend')
            ->find(1);
        var_dump($extend);
    }
}