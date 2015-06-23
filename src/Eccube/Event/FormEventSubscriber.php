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
        $basePath = __DIR__ . '/../../../app/Plugin';
        $finder = Finder::create()
            ->in($basePath)
            ->directories()
            ->depth(0);

        foreach ($finder as $dir) {
            $config = Yaml::parse($dir->getRealPath() . '/config.yml');
            
            if (isset($config['form'])) {
                foreach ($config['form'] as $event => $class) {
                    $events[$event][] = '\\Plugin\\' . $config['name'] . '\\' . $class;
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
