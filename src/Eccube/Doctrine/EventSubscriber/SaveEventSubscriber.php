<?php

namespace Eccube\Doctrine\EventSubscriber;

use Doctrine\ORM\Events;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\SecurityContextInterface;

class SaveEventSubscriber implements EventSubscriber
{
    /**
     * @var SecurityContext
     */
    private $security;

    /**
     * @param SecurityContext $config
     */
    public function __construct(SecurityContextInterface $security)
    {
        $this->security = $security;
    }

    public function getSubscribedEvents()
    {
        return array(
            Events::prePersist,
            Events::preUpdate,
        );
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (method_exists($entity, 'setCreateDate')) {
            $entity->setCreateDate(new \DateTime());
        }
        if (method_exists($entity, 'setUpdateDate')) {
            $entity->setUpdateDate(new \DateTime());
        }

        if ($this->security->isGranted('ROLE_ADMIN') && method_exists($entity, 'setCreator')) {
            $Member = $this->security->getToken()->getUser();
            $entity->setCreator($Member);
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();

        if (method_exists($entity, 'setUpdateDate')) {
            $entity->setUpdateDate(new \DateTime());
        }
    }
}
