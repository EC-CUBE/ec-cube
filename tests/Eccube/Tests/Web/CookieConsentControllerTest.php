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

namespace Eccube\Tests\Web;

use Eccube\Common\Constant;
use Eccube\Entity\BaseInfo;
use Eccube\Service\CookieConsentService;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CookieConsentControllerのテスト（Web層・HTTP統合テスト）
 */
final class CookieConsentControllerTest extends AbstractWebTestCase
{
    /**
     * 各テスト開始前にクッキーポリシー同意機能を ON（true）に設定する。
     * DAMADoctrineTestBundle によりテスト後に自動ロールバックされる。
     */
    protected function setUp(): void
    {
        parent::setUp();

        $BaseInfo = $this->entityManager->getRepository(BaseInfo::class)->get();
        if (!$BaseInfo->isOptionCookieConsent()) {
            $BaseInfo->setOptionCookieConsent(true);
            $this->entityManager->flush();
        }
    }

    public function testIndexDisplaysSettingsPage()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('cookie_consent_index'));

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testIndexShowsCurrentStatusAccepted()
    {
        $this->client->getCookieJar()->set(
            new Cookie(CookieConsentService::COOKIE_NAME, CookieConsentService::STATUS_ACCEPTED)
        );

        $this->client->request(Request::METHOD_GET, $this->generateUrl('cookie_consent_index'));

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertStringContainsString('同意', (string) $this->client->getResponse()->getContent());
    }

    public function testIndexShowsNotSetStatusWhenNoCookie()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('cookie_consent_index'));

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertStringContainsString('未設定', (string) $this->client->getResponse()->getContent());
    }

    /**
     * 機能 OFF のとき、設定ページはトップへリダイレクトする。
     */
    public function testIndexRedirectsToTopWhenFeatureDisabled()
    {
        $BaseInfo = $this->entityManager->getRepository(BaseInfo::class)->get();
        $BaseInfo->setOptionCookieConsent(false);
        $this->entityManager->flush();

        $this->client->request(Request::METHOD_GET, $this->generateUrl('cookie_consent_index'));

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('homepage')));
    }

    public function testPolicyDisplaysPolicyPage()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('help_cookie_policy'));

        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertStringContainsString('クッキーポリシー', (string) $this->client->getResponse()->getContent());
    }

    /**
     * 同意の更新で JSON 成功応答と Set-Cookie（accepted）が返ることを確認する。
     */
    public function testUpdateAcceptsConsentSuccessfully()
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('cookie_consent_update'),
            [
                'consent_status' => CookieConsentService::STATUS_ACCEPTED,
                'source' => 'popup',
                Constant::TOKEN_NAME => 'dummy',
            ]
        );

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful(), (string) $response->getContent());

        $json = json_decode((string) $response->getContent(), true);
        $this->assertTrue($json['success']);

        $found = false;
        foreach ($this->client->getResponse()->headers->getCookies() as $cookie) {
            if ($cookie->getName() === CookieConsentService::COOKIE_NAME) {
                $this->assertSame(CookieConsentService::STATUS_ACCEPTED, $cookie->getValue());
                $found = true;
            }
        }
        $this->assertTrue($found, 'eccube_cookie_consent Cookie が Set-Cookie されていること');
    }

    public function testUpdateRejectsConsentSuccessfully()
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('cookie_consent_update'),
            [
                'consent_status' => CookieConsentService::STATUS_REJECTED,
                'source' => 'popup',
                Constant::TOKEN_NAME => 'dummy',
            ]
        );

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful(), (string) $response->getContent());

        $found = false;
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === CookieConsentService::COOKIE_NAME) {
                $this->assertSame(CookieConsentService::STATUS_REJECTED, $cookie->getValue());
                $found = true;
            }
        }
        $this->assertTrue($found);
    }

    /**
     * ログイン中の会員でも同意を更新でき、JSON 成功応答と Set-Cookie が返ることを確認する
     * （会員 ID を採取するログ経路を通す）。
     */
    public function testUpdateAcceptsConsentForLoggedInCustomer()
    {
        $Customer = $this->createCustomer();
        $this->loginTo($Customer);

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('cookie_consent_update'),
            [
                'consent_status' => CookieConsentService::STATUS_ACCEPTED,
                'source' => 'settings_page',
                Constant::TOKEN_NAME => 'dummy',
            ]
        );

        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful(), (string) $response->getContent());

        $json = json_decode((string) $response->getContent(), true);
        $this->assertTrue($json['success']);

        $found = false;
        foreach ($response->headers->getCookies() as $cookie) {
            if ($cookie->getName() === CookieConsentService::COOKIE_NAME) {
                $this->assertSame(CookieConsentService::STATUS_ACCEPTED, $cookie->getValue());
                $found = true;
            }
        }
        $this->assertTrue($found);
    }

    /**
     * 不正な consent_status は 400 を返し、Cookie を設定しない。
     *
     * @param mixed $invalidStatus 不正な同意状態値
     */
    #[DataProvider(methodName: 'provide_invalid_consent_status_patterns')]
    public function testUpdateRejectsInvalidConsentStatusValue(mixed $invalidStatus)
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('cookie_consent_update'),
            [
                'consent_status' => $invalidStatus,
                Constant::TOKEN_NAME => 'dummy',
            ]
        );

        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    /**
     * @return \Iterator<string, array{mixed}>
     */
    public static function provide_invalid_consent_status_patterns(): \Iterator
    {
        yield '空文字' => [''];
        yield '不正な値' => ['invalid'];
        yield '大文字' => ['ACCEPTED'];
    }

    /**
     * consent_status 未指定は 400 を返す。
     */
    public function testUpdateRejectsMissingConsentStatus()
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('cookie_consent_update'),
            [
                Constant::TOKEN_NAME => 'dummy',
            ]
        );

        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    /**
     * 機能 OFF のとき、更新 API は 404 を返す（index() のリダイレクトと挙動を揃える）。
     */
    public function testUpdateReturnsNotFoundWhenFeatureDisabled()
    {
        $BaseInfo = $this->entityManager->getRepository(BaseInfo::class)->get();
        $BaseInfo->setOptionCookieConsent(false);
        $this->entityManager->flush();

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('cookie_consent_update'),
            [
                'consent_status' => CookieConsentService::STATUS_ACCEPTED,
                'source' => 'popup',
                Constant::TOKEN_NAME => 'dummy',
            ]
        );

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }
}
