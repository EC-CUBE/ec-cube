<?php

namespace Eccube\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ConvertTelListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT   => 'onPreSubmit',
        );
    }

    public function onPreSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $data = mb_convert_kana($data,"an","UTF-8");
        $data = str_replace("-","",$data);
        $event->setData($data);
    }
}
