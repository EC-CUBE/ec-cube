<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\LoginHistory;
use Eccube\Entity\Master\LoginHistoryStatus;
use Eccube\Entity\Member;
use Eccube\Repository\Master\LoginHistoryStatusRepository;
use Eccube\Repository\MemberRepository;
use Eccube\Request\Context;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class LoginHistoryListener implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var Context
     */
    private $requestContext;
    /**
     * @var MemberRepository
     */
    private $memberRepository;

    /**
     * @var LoginHistoryStatusRepository
     */
    private $loginHistoryStatusRepository;

    public function __construct(
        EntityManagerInterface $em,
        RequestStack $requestStack,
        Context $requestContext,
        MemberRepository $memberRepository,
        LoginHistoryStatusRepository $loginHistoryStatusRepository
    ) {
        $this->entityManager = $em;
        $this->requestStack = $requestStack;
        $this->requestContext = $requestContext;
        $this->memberRepository = $memberRepository;
        $this->loginHistoryStatusRepository = $loginHistoryStatusRepository;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
            AuthenticationEvents::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
        ];
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $request = $event->getRequest();
        $user = $event
            ->getAuthenticationToken()
            ->getUser();

        $Status = $this->loginHistoryStatusRepository->find(LoginHistoryStatus::SUCCESS);
        if (is_null($Status)) {
            return;
        }

        if ($user instanceof Member) {
            $LoginHistory = new LoginHistory();
            $LoginHistory
                ->setLoginUser($user)
                ->setUserName($user->getUsername())
                ->setStatus($Status)
                ->setClientIp($request->getClientIp());

            $this->entityManager->persist($LoginHistory);
            $this->entityManager->flush();
        }
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$this->requestContext->isAdmin()) {
            return;
        }

        $Status = $this->loginHistoryStatusRepository->find(LoginHistoryStatus::FAILURE);
        if (is_null($Status)) {
            return;
        }

        $userName = $event->getAuthenticationToken()->getUsername();
        $Member = null;
        if ($userName) {
            $Member = $this->memberRepository->findOneBy(['login_id' => $userName]);
        }

        $LoginHistory = new LoginHistory();
        $LoginHistory
            ->setLoginUser($Member)
            ->setUserName($userName)
            ->setStatus($Status)
            ->setClientIp($request->getClientIp());

        $this->entityManager->persist($LoginHistory);
        $this->entityManager->flush();
    }
}
