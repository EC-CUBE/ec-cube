<?php

declare(strict_types=1);

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

namespace Eccube\Tests\Twig\Extension;

use Eccube\Service\CookieConsentService;
use Eccube\Tests\EccubeTestCase;
use Eccube\Twig\Extension\CookieConsentExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * CookieConsentExtensionのテスト
 */
final class CookieConsentExtensionTest extends EccubeTestCase
{
    private function createExtension(?string $cookieValue): CookieConsentExtension
    {
        $request = new Request();
        if ($cookieValue !== null) {
            $request->cookies->set(CookieConsentService::COOKIE_NAME, $cookieValue);
        }
        $requestStack = new RequestStack();
        $requestStack->push($request);

        return new CookieConsentExtension(new CookieConsentService(), $requestStack);
    }

    public function testIsConsentGiven()
    {
        $this->assertTrue($this->createExtension('accepted')->isConsentGiven());
        $this->assertTrue($this->createExtension('rejected')->isConsentGiven());
        $this->assertFalse($this->createExtension(null)->isConsentGiven());
    }

    public function testIsConsentAccepted()
    {
        $this->assertTrue($this->createExtension('accepted')->isConsentAccepted());
        $this->assertFalse($this->createExtension('rejected')->isConsentAccepted());
        $this->assertFalse($this->createExtension(null)->isConsentAccepted());
    }

    public function testGetConsentStatus()
    {
        $this->assertSame('accepted', $this->createExtension('accepted')->getConsentStatus());
        $this->assertSame('rejected', $this->createExtension('rejected')->getConsentStatus());
        $this->assertNull($this->createExtension(null)->getConsentStatus());
    }

    /**
     * Request が無い場合（CLI コンテキスト等）でも安全にフォールバックすることを確認する。
     */
    public function testFallbackWhenNoRequest()
    {
        $extension = new CookieConsentExtension(new CookieConsentService(), new RequestStack());

        $this->assertFalse($extension->isConsentGiven());
        $this->assertFalse($extension->isConsentAccepted());
        $this->assertNull($extension->getConsentStatus());
    }
}
