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

namespace Eccube\Tests\EventListener;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Customer;
use Eccube\EventListener\RateLimiterListener;
use Eccube\Request\Context;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class RateLimiterListenerTest extends EccubeTestCase
{
    /**
     * @dataProvider onControllerProvider
     */
    public function testOnController($limiterId, $type, $params)
    {
        $request = $this->createStub(Request::class);
        $request->method('getClientIp')
            ->willReturn('127.0.0.1');
        $request->method('getMethod')
            ->willReturn('POST');
        $request->method('get')
            ->will($this->returnValueMap([
                ['mode', null, 'complete'],
                ['next', null, 'confirm'],
            ]));

        $request->attributes = new ParameterBag();
        $request->attributes->set('_route', 'test');

        $Customer = $this->createStub(Customer::class);
        $Customer->method('getId')
            ->willReturn(1);

        $context = $this->createStub(Context::class);
        $context->method('getCurrentUser')
            ->willReturn($Customer);

        $event = new ControllerEvent(self::$kernel, function () {}, $request, HttpKernelInterface::MAIN_REQUEST);

        $map = [
            [
                'eccube_rate_limiter_configs',
                [
                    'test' => [
                        $limiterId => [
                            'method' => ['POST'],
                            'type' => [$type],
                            'params' => $params,
                        ],
                    ],
                ],
            ],
        ];

        $config = $this->createStub(EccubeConfig::class);
        $config->method('offsetGet')
            ->will($this->returnValueMap($map));

        $i = 0;
        $listener = new RateLimiterListener($this->getContainer(), $config, $context);

        try {
            $i++;
            $listener->onController($event);
            $i++;
            $listener->onController($event);
            self::fail();
        } catch (\Exception $e) {
            self::assertInstanceOf(TooManyRequestsHttpException::class, $e);
        }

        // 2回目でTooManyRequestsHttpExceptionがthrowされる.
        // キャッシュが残っている場合は、bin/console cache:pool:clear rate_limiter.cache --env=test を実行する
        self::assertSame(2, $i);
    }

    public function onControllerProvider()
    {
        return [
            ['test_ip', 'ip', []],
            ['test_customer', 'customer', []],
            ['test_params', 'customer', ['mode' => 'complete', 'next' => 'confirm']],
        ];
    }

    public function testGetSubscribedEvents()
    {
        self::assertSame(
            [KernelEvents::CONTROLLER => ['onController', 0]],
            RateLimiterListener::getSubscribedEvents()
        );
    }
}
