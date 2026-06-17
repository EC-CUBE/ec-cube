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

namespace Eccube\Controller;

use Eccube\Entity\Customer;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Service\CookieConsentLogService;
use Eccube\Service\CookieConsentService;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/**
 * クッキー同意機能のコントローラ。
 *
 * 設定ページ・ポリシーページの表示と、同意状態更新 API（Ajax）を提供する。
 * 同意 Cookie はこの更新 API のレスポンス（Set-Cookie）でのみ設定する。
 */
class CookieConsentController extends AbstractController
{
    public function __construct(
        protected CookieConsentService $cookieConsentService,
        protected CookieConsentLogService $cookieConsentLogService,
        protected BaseInfoRepository $baseInfoRepository,
    ) {
    }

    /**
     * クッキー設定ページを表示する。
     *
     * クッキーポリシー同意機能が OFF の場合はトップページへリダイレクトする。
     *
     * @return array<string, mixed>|RedirectResponse
     */
    #[Route(path: '/cookie-consent', name: 'cookie_consent_index', methods: ['GET'])]
    #[Template(template: 'CookieConsent/cookie_consent.twig')]
    public function index(Request $request): array|RedirectResponse
    {
        $BaseInfo = $this->baseInfoRepository->get();
        if (!$BaseInfo->isOptionCookieConsent()) {
            return $this->redirectToRoute('homepage');
        }

        $currentStatus = $this->cookieConsentService->getConsentStatus($request);

        return [
            'current_status' => $currentStatus,
        ];
    }

    /**
     * クッキーポリシーページを表示する。
     *
     * @return array<string, mixed>
     */
    #[Route(path: '/help/cookie-policy', name: 'help_cookie_policy', methods: ['GET'])]
    #[Template(template: 'CookieConsent/cookie_policy.twig')]
    public function policy(): array
    {
        return [];
    }

    /**
     * 同意状態を更新する（Ajax API）。
     *
     * CSRF トークン検証必須。同意 Cookie はこのレスポンスの Set-Cookie でのみ設定する。
     * 操作記録のログ出力はベストエフォートで、失敗しても Cookie 設定・画面動作は継続する。
     */
    #[Route(path: '/cookie_consent/update', name: 'cookie_consent_update', methods: ['POST'])]
    public function update(Request $request): JsonResponse
    {
        // CSRFトークン検証
        if (!$this->isTokenValid()) {
            return $this->json([
                'success' => false,
                'message' => trans('cookie_consent.error.invalid_token'),
            ], 403);
        }

        // パラメータ取得
        $consentStatus = $request->request->get('consent_status');
        $source = $request->request->get('source', 'popup');
        $previousStatus = $request->request->get('previous_status');

        // バリデーション
        if (!in_array($consentStatus, [CookieConsentService::STATUS_ACCEPTED, CookieConsentService::STATUS_REJECTED], true)) {
            return $this->json([
                'success' => false,
                'message' => trans('cookie_consent.error.invalid_status'),
            ], 400);
        }

        // ログデータを構築
        $customer = $this->getUser();
        $customerId = ($customer instanceof Customer) ? $customer->getId() : null;
        $sessionId = $this->session->getId();
        $ipAddress = $request->getClientIp() ?? '';
        $userAgent = $request->headers->get('User-Agent') ?? '';

        $logData = $this->cookieConsentLogService->buildLogData(
            $consentStatus,
            $customerId,
            $sessionId,
            $ipAddress,
            $userAgent,
            is_string($source) ? $source : 'popup',
            is_string($previousStatus) ? $previousStatus : null
        );

        // 操作記録のログ出力（ベストエフォート：失敗してもユーザー体験を損なわない）
        $this->cookieConsentLogService->saveLog($logData);

        // 同意状態を Cookie に保存（Set-Cookie はサーバ側でのみ設定）
        $response = $this->json([
            'success' => true,
            'message' => trans('cookie_consent.page.save_success'),
        ]);

        $this->cookieConsentService->saveConsentStatus($response, $consentStatus, $request);

        return $response;
    }
}
