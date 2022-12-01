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
use Eccube\Entity\Customer;
use Eccube\Request\Context;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactory;

class RateLimiterListener implements EventSubscriberInterface
{
    private ContainerInterface $locator;
    private EccubeConfig $eccubeConfig;
    private Context $requestContext;

    public function __construct(ContainerInterface $locator, EccubeConfig $eccubeConfig, Context $requestContext)
    {
        $this->locator = $locator;
        $this->eccubeConfig = $eccubeConfig;
        $this->requestContext = $requestContext;
    }

    public function onController(ControllerEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if (!is_array($event->getController())) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');
        $limiterConfigs = $this->eccubeConfig['eccube_rate_limiter_configs'];

        if (!isset($limiterConfigs[$route])) {
            return;
        }

        $method = $request->getMethod();
        $mode = $request->get('mode');

        foreach ($limiterConfigs[$route] as $id => $config) {
            $methods = array_filter($config['method'], fn ($m) => $m === $method);
            if (empty($methods)) {
                // http methodが不一致であればスキップ
                continue;
            }

            if (isset($config['mode']) && $mode !== $config['mode']) {
                // modeパラメータが不一致であればスキップ
                continue;
            }

            $limiterId = 'limiter.'.$id;
            if (!$this->locator->has($limiterId)) {
                continue;
            }

            /** @var RateLimiterFactory $factory */
            $factory = $this->locator->get($limiterId);
            if ('customer' === $config['type']) {
                $User = $this->requestContext->getCurrentUser();
                if (!$User instanceof Customer) {
                    // 会員の未ログイン時はスキップ
                    continue;
                }
                $limiter = $factory->create($User->getId());
            } else {
                // default ip.
                $limiter = $factory->create($request->getClientIp());
            }

            if (!$limiter->consume()->isAccepted()) {
                throw new TooManyRequestsHttpException();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => ['onController', 0],
        ];
    }
}
