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

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Member;
use Eccube\Request\Context;
use Eccube\Service\TwoFactorAuthService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TwoFactorAuthListener implements EventSubscriberInterface
{
    /**
     * @var array<string> 2段階認証のチェックを除外するroute
     */
    public const ROUTE_EXCLUDE = ['admin_two_factor_auth'];

    /**
     * @var array<string> 2段階認証キー未設定時のみ除外するroute
     */
    public const ROUTE_EXCLUDE_WHEN_NOT_CONFIGURED = ['admin_two_factor_auth_set'];

    /**
     * @param Context $requestContext,
     */
    public function __construct(protected EccubeConfig $eccubeConfig, protected Context $requestContext, protected UrlGeneratorInterface $router, protected TwoFactorAuthService $twoFactorAuthService)
    {
    }

    public function onKernelController(ControllerArgumentsEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if (!$this->requestContext->isAdmin()) {
            return;
        }

        if (!$this->twoFactorAuthService->isEnabled()) {
            return;
        }

        $route = $event->getRequest()->attributes->get('_route');
        if (in_array($route, self::ROUTE_EXCLUDE)) {
            return;
        }

        $Member = $this->requestContext->getCurrentUser();

        // 2FAキー未設定時のみ除外するルートのチェック
        // 既に2FAキーが設定されている場合は除外しない（認証が必要）
        if (in_array($route, self::ROUTE_EXCLUDE_WHEN_NOT_CONFIGURED)) {
            if ($Member instanceof Member && !$Member->getTwoFactorAuthKey()) {
                return;
            }
        }

        if (
            $Member instanceof Member
            && $Member->isTwoFactorAuthEnabled()
            && !$this->twoFactorAuthService->isAuth($Member)
        ) {
            // トークン入力
            if ($Member->getTwoFactorAuthKey()) {
                $url = $this->router->generate('admin_two_factor_auth', [], UrlGeneratorInterface::ABSOLUTE_PATH);
            }

            // 2段階認証設定
            else {
                $url = $this->router->generate('admin_two_factor_auth_set', [], UrlGeneratorInterface::ABSOLUTE_PATH);
            }
            $event->setController(fn () => new RedirectResponse($url, $status = 302));
        }
    }

    /**
     * @return array<string, array<int|string>>
     */
    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => ['onKernelController', 7],
        ];
    }
}
