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
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class TwoFactorAuthListener implements EventSubscriberInterface
{
    /**
     * @var array 2段階認証のチェックを除外するroute
     */
    const ROUTE_EXCLUDE = ['admin_two_factor_auth', 'admin_two_factor_auth_set'];

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var Context
     */
    protected $requestContext;

    /**
     * @var UrlGeneratorInterface
     */
    protected $router;

    /**
     * @var TwoFactorAuthService
     */
    protected $twoFactorAuthService;

    /**
     * @param EccubeConfig $eccubeConfig
     * @param Context $context,
     * @param UrlGeneratorInterface $router
     * @param EncoderFactoryInterface $encoderFactory
     */
    public function __construct(
        EccubeConfig $eccubeConfig,
        Context $requestContext,
        UrlGeneratorInterface $router,
        TwoFactorAuthService $twoFactorAuthService
    ) {
        $this->eccubeConfig = $eccubeConfig;
        $this->requestContext = $requestContext;
        $this->router = $router;
        $this->twoFactorAuthService = $twoFactorAuthService;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        if (!$event->isMasterRequest()) {
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

        if (
            ($Member = $this->requestContext->getCurrentUser())
            && $Member instanceof Member
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
            $event->setController(function () use ($url) {
                return new RedirectResponse($url, $status = 302);
            });
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => ['onKernelController', 7],
        ];
    }
}
