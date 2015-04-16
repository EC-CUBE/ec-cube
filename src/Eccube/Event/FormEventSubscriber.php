<?php

namespace Eccube\Event;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class FormEventSubscriber implements EventSubscriberInterface
{
    public static function getEvents()
    {
        $events = array();
        // YamlでParseしてがんばる
        $basePath = __DIR__ . '/../../../app/plugin';
        $finder = Finder::create()
            ->in($basePath)
            ->directories()
            ->depth(0);

        foreach ($finder as $dir) {
            $config = Yaml::parse($dir->getRealPath() . '/config.yml');
            
            if ($config['enable'] === true) {
                if (isset($config['form'])) {
                    foreach ($config['form'] as $event => $class) {
                        $events[$event][] = '\\Plugin\\' . $config['name'] . '\\' . $class;
                    }
                }
            }
        }

        return $events;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::POST_SET_DATA => 'onPostSetData',
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
            FormEvents::SUBMIT => 'onSubmit',
            FormEvents::POST_SUBMIT => 'onPostSubmit',
        );
    }

    public function onPreSetData(FormEvent $event)
    {
        $events = self::getEvents();

        if (isset($events['onPreSetData'])) {
            foreach($events['onPreSetData'] as $formEventClass) {
                $formEvent = new $formEventClass();
                $formEvent->onPreSetData($event);
            }
        }
    }

    public function onPostSetData(FormEvent $event)
    {
        $events = self::getEvents();

        if (isset($events['onPostSetData'])) {
            foreach($events['onPostSetData'] as $formEventClass) {
                $formEvent = new $formEventClass();
                $formEvent->onPostSetData($event);
            }
        }
    }

    public function onPreSubmit(FormEvent $event)
    {
        $events = self::getEvents();

        if (isset($events['onPreSubmit'])) {
            foreach($events['onPreSubmit'] as $formEventClass) {
                $formEvent = new $formEventClass();
                $formEvent->onPreSubmit($event);
            }
        }
    }

    public function onSubmit(FormEvent $event)
    {
        $events = self::getEvents();

        if (isset($events['onSubmit'])) {
            foreach($events['onSubmit'] as $formEventClass) {
                $formEvent = new $formEventClass();
                $formEvent->onSubmit($event);
            }
        }
    }

    public function onPostSubmit(FormEvent $event)
    {
        $events = self::getEvents();

        if (isset($events['onPostSubmit'])) {
            foreach($events['onPostSubmit'] as $formEventClass) {
                $formEvent = new $formEventClass();
                $formEvent->onPostSubmit($event);
            }
        }
    }
}