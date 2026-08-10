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
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class LoginHistoryListener implements EventSubscriberInterface
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly RequestStack $requestStack, private readonly Context $requestContext, private readonly MemberRepository $memberRepository, private readonly LoginHistoryStatusRepository $loginHistoryStatusRepository)
    {
    }

    /**
     * @return array<string, string>
     */
    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
            LoginFailureEvent::class => 'onAuthenticationFailure',
        ];
    }

    public function onInteractiveLogin(InteractiveLoginEvent $event): void
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
                ->setClientIp($request->getClientIp())
                ->setCreateDate(new \DateTime())
                ->setUpdateDate(new \DateTime());

            $this->entityManager->persist($LoginHistory);
            $this->entityManager->flush();
        }
    }

    public function onAuthenticationFailure(LoginFailureEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$this->requestContext->isAdmin()) {
            return;
        }

        $Status = $this->loginHistoryStatusRepository->find(LoginHistoryStatus::FAILURE);
        if (is_null($Status)) {
            return;
        }

        // Bearer/AccessToken 系認証で passport 生成前に失敗した場合は null になる.
        // UserBadge を持たない場合もユーザーを特定できないため、ログイン履歴の記録対象外とする.
        $passport = $event->getPassport();
        if ($passport === null || !$passport->hasBadge(UserBadge::class)) {
            return;
        }

        $userName = $passport->getBadge(UserBadge::class)->getUserIdentifier();
        $Member = $this->memberRepository->findOneBy(['login_id' => $userName]);

        $LoginHistory = new LoginHistory();
        $LoginHistory
            ->setLoginUser($Member)
            ->setUserName($userName)
            ->setStatus($Status)
            ->setClientIp($request->getClientIp())
            ->setCreateDate(new \DateTime())
            ->setUpdateDate(new \DateTime());

        $this->entityManager->persist($LoginHistory);
        $this->entityManager->flush();
    }
}
