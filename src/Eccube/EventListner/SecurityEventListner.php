<?php

namespace Eccube\EventListner;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityEventListner
{
    public $entityManager;

    /**
     * @param \Doctrine\ORM\EntityManager $entityManager
     */
    public function __construct(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $token = $event->getAuthenticationToken();

        $user = $token->getUser();
        if ($user instanceof \Eccube\Entity\Member) {
            $user->setLoginDate(new \DateTime());
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }
}
