<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
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


namespace Eccube\Entity\Event;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Eccube\Annotation\TargetEntity;
use Eccube\Annotation\TargetEvent;

class EntityEventDispatcher
{

    private $eventListeners = [];

    private $entityManager;

    function __construct(EntityManager $em)
    {
        $this->entityManager = $em;
    }

    public function addEventListener(EntityEventListener $listener)
    {
        $reader = new AnnotationReader();
        $rc = new \ReflectionClass($listener);
        $annotations = $reader->getClassAnnotations($rc);
        foreach ($annotations as $ann) {
            $eventAnn = $reader->getClassAnnotation(new \ReflectionClass($ann), TargetEvent::class);
            if ($eventAnn) {
                if (!isset($this->eventListeners[$eventAnn->value])) {
                    $this->entityManager->getEventManager()->addEventListener($eventAnn->value, $this);
                }
                foreach ($ann->value as $targetClass) {
                    $this->eventListeners[$eventAnn->value][$targetClass][] = $listener;
                }
            }
        }
    }

    public function preRemove(LifecycleEventArgs $eventArgs)
    {
        $this->handle(Events::preRemove, $eventArgs);
    }

    public function postRemove(LifecycleEventArgs $eventArgs)
    {
        $this->handle(Events::postRemove, $eventArgs);
    }

    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $this->handle(Events::prePersist, $eventArgs);
    }

    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $this->handle(Events::postPersist, $eventArgs);
    }

    public function preUpdate(LifecycleEventArgs $eventArgs)
    {
        $this->handle(Events::preUpdate, $eventArgs);
    }

    public function postUpdate(LifecycleEventArgs $eventArgs)
    {
        $this->handle(Events::postUpdate, $eventArgs);
    }

    public function postLoad(LifecycleEventArgs $eventArgs)
    {
        $this->handle(Events::postLoad, $eventArgs);
    }

    public function loadClassMetadata(LifecycleEventArgs $eventArgs)
    {
        $this->handle(Events::loadClassMetadata, $eventArgs);
    }

    public function onClassMetadataNotFound(LifecycleEventArgs $eventArgs)
    {
        $this->handle(Events::onClassMetadataNotFound, $eventArgs);
    }

    public function onFlush(LifecycleEventArgs $eventArgs)
    {
        $this->handle(Events::onFlush, $eventArgs);
    }

    public function postFlush(LifecycleEventArgs $eventArgs)
    {
        $this->handle(Events::postFlush, $eventArgs);
    }

    public function onClear(LifecycleEventArgs $eventArgs)
    {
        $this->handle(Events::onClear, $eventArgs);
    }

    private function handle($eventName, LifecycleEventArgs $eventArgs)
    {
        $listeners = $this->getEventListeners($eventName);
        $entityClass = get_class($eventArgs->getEntity());
        if (isset($listeners[$entityClass])) {
            array_walk($listeners[$entityClass], function(EntityEventListener $listener) use ($eventArgs) {
                $listener->execute($eventArgs);
            });
        }
    }

    public function getEventListeners($targetEvent)
    {
        return isset($this->eventListeners[$targetEvent]) ? $this->eventListeners[$targetEvent] : [];
    }
}