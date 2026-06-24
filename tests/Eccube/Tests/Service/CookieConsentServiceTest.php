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

namespace Eccube\Tests\Service;

use Eccube\Service\CookieConsentService;
use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CookieConsentServiceのテスト
 */
final class CookieConsentServiceTest extends EccubeTestCase
{
    private ?CookieConsentService $service = null;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CookieConsentService();
    }

    /**
     * Cookie 値から同意状態を正しく取得できることを確認する。
     *
     * @param string|null $cookieValue Cookie値
     * @param string|null $expected 期待値
     */
    #[DataProvider(methodName: 'provide_consent_status_patterns')]
    public function testGetConsentStatusReturnsCorrectValue(?string $cookieValue, ?string $expected)
    {
        $request = new Request();
        if ($cookieValue !== null) {
            $request->cookies->set(CookieConsentService::COOKIE_NAME, $cookieValue);
        }

        $result = $this->service->getConsentStatus($request);

        $this->assertSame($expected, $result);
    }

    /**
     * 同意状態パターンのデータプロバイダ
     *
     * @return \Iterator<string, array{(string|null), (string|null)}>
     */
    public static function provide_consent_status_patterns(): \Iterator
    {
        yield '同意済み' => ['accepted', 'accepted'];
        yield '拒否' => ['rejected', 'rejected'];
        yield '未設定' => [null, null];
        yield '不正な値' => ['invalid', 'invalid'];
    }

    /**
     * 同意 Cookie が正しい属性（Secure は isSecure 連動、HttpOnly=false、SameSite=Lax）で設定されることを確認する。
     *
     * @param string $status 同意状態
     * @param bool $isSecure HTTPS環境かどうか
     * @param bool $expectedSecure 期待される Secure フラグ
     */
    #[DataProvider(methodName: 'provide_save_consent_patterns')]
    public function testSaveConsentStatusSetsCookieWithCorrectAttributes(string $status, bool $isSecure, bool $expectedSecure)
    {
        $request = Request::create('/', Request::METHOD_GET, [], [], [], [
            'HTTPS' => $isSecure ? 'on' : 'off',
        ]);
        $response = new Response();

        $this->service->saveConsentStatus($response, $status, $request);

        $cookies = $response->headers->getCookies();
        $this->assertCount(1, $cookies);

        $cookie = $cookies[0];
        $this->assertInstanceOf(Cookie::class, $cookie);
        $this->assertSame(CookieConsentService::COOKIE_NAME, $cookie->getName());
        $this->assertSame($status, $cookie->getValue());
        $this->assertSame('/', $cookie->getPath());
        $this->assertSame($expectedSecure, $cookie->isSecure());
        $this->assertFalse($cookie->isHttpOnly());
        $this->assertSame(Cookie::SAMESITE_LAX, $cookie->getSameSite());
    }

    /**
     * Cookie 有効期限が 365 日であることを確認する。
     */
    public function testSaveConsentStatusSetsCookieLifetimeTo365Days()
    {
        $request = Request::create('/', Request::METHOD_GET);
        $response = new Response();
        $beforeTime = time();

        $this->service->saveConsentStatus($response, 'accepted', $request);

        $cookies = $response->headers->getCookies();
        $cookie = $cookies[0];

        // 有効期限は現在時刻 + 365日（31,536,000秒）
        $expectedExpireTime = $beforeTime + (365 * 86400);
        $actualExpireTime = $cookie->getExpiresTime();

        // タイムスタンプの誤差を2秒以内として許容
        $this->assertEqualsWithDelta($expectedExpireTime, $actualExpireTime, 2);
    }

    /**
     * 同意状態保存パターンのデータプロバイダ
     *
     * @return \Iterator<string, array{string, bool, bool}>
     */
    public static function provide_save_consent_patterns(): \Iterator
    {
        yield '同意・HTTPS' => ['accepted', true, true];
        yield '同意・HTTP' => ['accepted', false, false];
        yield '拒否・HTTPS' => ['rejected', true, true];
        yield '拒否・HTTP' => ['rejected', false, false];
    }
}
