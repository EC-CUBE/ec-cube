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
use Eccube\Entity\Customer;
use Eccube\Entity\LoginHistory;
use Eccube\Entity\Master\LoginHistoryStatus;
use Eccube\Entity\Member;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\Master\LoginHistoryStatusRepository;
use Eccube\Repository\MemberRepository;
use Eccube\Request\Context;
use Eccube\Service\MailService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
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

    /** @var MailService */
    protected $mailService;

    /** @var CustomerRepository */
    protected $customerRepository;

    public function __construct(
        EntityManagerInterface $em,
        RequestStack $requestStack,
        Context $requestContext,
        MemberRepository $memberRepository,
        LoginHistoryStatusRepository $loginHistoryStatusRepository,
        MailService $mailService,
        CustomerRepository $customerRepository
    ) {
        $this->entityManager = $em;
        $this->requestStack = $requestStack;
        $this->requestContext = $requestContext;
        $this->memberRepository = $memberRepository;
        $this->loginHistoryStatusRepository = $loginHistoryStatusRepository;
        $this->mailService = $mailService;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
            LoginFailureEvent::class => 'onAuthenticationFailure',
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

            $this->mailService->sendAdminEventNotifyMail($user, $request, trans('admin.login.login'));
        } elseif ($user instanceof Customer) {
            $this->mailService->sendEventNotifyMail($user, $request, trans('admin.login.login'));
        }
    }

    public function onAuthenticationFailure(LoginFailureEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();
        $Status = $this->loginHistoryStatusRepository->find(LoginHistoryStatus::FAILURE);
        if (is_null($Status)) {
            return;
        }

        $Member = null;
        $userName = null;
        $passport = $event->getPassport();
        if ($passport->hasBadge(UserBadge::class)) {
            $userName = $passport->getBadge(UserBadge::class)
                ->getUserIdentifier();

            if ($this->requestContext->isAdmin()) {
                $Member = $this->memberRepository->findOneBy(['login_id' => $userName]);
                if ($Member instanceof Member) {
                    $this->mailService->sendAdminEventNotifyMail($Member, $request, trans('admin.login.failure.notify_title'));
                }
            } else {
                $Customer = $this->customerRepository->findOneBy(['email' => $userName]);
                if ($Customer instanceof Customer) {
                    $this->mailService->sendEventNotifyMail($Customer, $request, trans('admin.login.failure.notify_title'));
                }
            }
        }

        if (!$this->requestContext->isAdmin()) {
            return;
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
