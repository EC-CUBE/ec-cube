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

use Eccube\Tests\Web\AbstractWebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Eccube\Common\EccubeConfig;
use Eccube\Request\Context;
use Eccube\EventListener\IpAddrListener;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class IpAddrListenerTest extends AbstractWebTestCase
{

    protected $clientIp = '192.168.56.1';

    public function ipAddressParams()
    {
        // 第1要素：許可IPリスト
        // 第2要素：拒否IPリスト
        // 第3要素：想定結果（許可->true、拒否->false）
        return [
            // allowチェック 許可パターン
            [[], [], true], // 空
            [['192.168.56.1'], [], true], // IPアドレスのみ
            [['192.168.56.1/32'], [], true], // IPアドレスとビットマスク最大値
            [['127.0.0.1', '192.168.56.1/32'], [],  true], // 複数行に渡る記述

            // allowチェック 拒否パターン
            [['192.168.56.2'], [], false], // IPアドレスのみ
            [['192.168.56.2/32'], [], false], // IPアドレスとビットマスク最大値
            [['127.0.0.1', '192.168.56.2/32'], [],  false], // 複数行に渡る記述

            // denyチェック 拒否パターン
            [[], ['192.168.56.1'], false], // IPアドレスのみ
            [[], ['192.168.56.1/32'], false], // IPアドレスとビットマスク最大値
            [[], ['127.0.0.1', '192.168.56.1/32'], false], // 複数行に渡る記述
            [['192.168.56.1/32'], ['192.168.56.1/32'], false], // 許可リストで許可後、拒否リストに同様の記述があるため結果拒否される

            // denyチェック 許可パターン
            [[], ['192.168.56.2'], true], // IPアドレスのみ
            [[], ['192.168.56.2/32'], true], // IPアドレスとビットマスク最大値
            [[], ['127.0.0.1', '192.168.56.2/32'],  true], // 複数行に渡る記述

        ];
    }


    /**
     * @dataProvider ipAddressParams
     */    
    public function testOnKernelRequest($allowHost, $denyHost, $expected)
    {
        $event = $this->createStub(RequestEvent::class);
        $event->method('isMainRequest')
            ->willReturn(true);

        $context = $this->createStub(Context::class);
        $context->method('isAdmin')
            ->willReturn(false);

        $map = [
            ['eccube_front_allow_hosts', $allowHost],
            ['eccube_front_deny_hosts',  $denyHost],
        ];
        $eccubeConfig = $this->createStub(EccubeConfig::class);
        $eccubeConfig->method('offsetGet')
            ->will($this->returnValueMap($map));

        $request = $this->createStub(Request::class);
        $request->method('getClientIp')
            ->willReturn($this->clientIp);

        $event->method('getRequest')
            ->willReturn($request);

        $ipAddrListerner = new IpAddrListener($eccubeConfig, $context);

        $actual = true;
        try {
            $ipAddrListerner->onKernelRequest($event);
        } catch (AccessDeniedHttpException $e) {
            $actual = false;
        }

        $this->assertSame($expected, $actual);
    }


        /**
     * @dataProvider ipAddressParams
     */    
    public function testOnKernelRequesAdmin($allowHost, $denyHost, $expected)
    {
        $event = $this->createStub(RequestEvent::class);
        $event->method('isMainRequest')
            ->willReturn(true);

        $context = $this->createStub(Context::class);
        $context->method('isAdmin')
            ->willReturn(true);

        $map = [
            ['eccube_admin_allow_hosts', $allowHost],
            ['eccube_admin_deny_hosts',  $denyHost],
        ];
        $eccubeConfig = $this->createStub(EccubeConfig::class);
        $eccubeConfig->method('offsetGet')
            ->will($this->returnValueMap($map));

        $request = $this->createStub(Request::class);
        $request->method('getClientIp')
            ->willReturn($this->clientIp);

        $event->method('getRequest')
            ->willReturn($request);

        $ipAddrListerner = new IpAddrListener($eccubeConfig, $context);

        $actual = true;
        try {
            $ipAddrListerner->onKernelRequest($event);
        } catch (AccessDeniedHttpException $e) {
            $actual = false;
        }

        $this->assertSame($expected, $actual);
    }
}
