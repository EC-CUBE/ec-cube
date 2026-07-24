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
    #[Route(path: '/cookie-consent/update', name: 'cookie_consent_update', methods: ['POST'])]
    public function update(Request $request): JsonResponse
    {
        // 機能 OFF のときは API も無効（index() がトップへリダイレクトするのと挙動を揃える）
        if (!$this->baseInfoRepository->get()->isOptionCookieConsent()) {
            throw $this->createNotFoundException();
        }

        // CSRFトークン検証（失敗時は isTokenValid() が AccessDeniedHttpException を投げ 403 を返す）
        $this->isTokenValid();

        // パラメータ取得
        // InputBag::get()/getString() は配列など非スカラー入力で BadRequestException を投げ、
        // 下の allowlist 検証（クリーンな 400 JSON）へ到達できない。all() で生値を受け取り、
        // 想定外の型・値は後続の検証／正規化で弾く。
        $params = $request->request->all();
        $consentStatus = $params['consent_status'] ?? null;
        $source = $params['source'] ?? CookieConsentService::SOURCE_POPUP;
        $previousStatus = $params['previous_status'] ?? null;

        // バリデーション（consent_status は許可値のみ。非スカラーも in_array で false になり 400 へ落ちる）
        if (!in_array($consentStatus, [CookieConsentService::STATUS_ACCEPTED, CookieConsentService::STATUS_REJECTED], true)) {
            return $this->json([
                'success' => false,
                'message' => trans('cookie_consent.error.invalid_status'),
            ], 400);
        }

        // source / previous_status を許可値へ正規化（getConsentStatus() と同じ allowlist 方式）。
        // 任意文字列や非スカラーがログ context へ素通しされるのを防ぐ。
        $source = in_array($source, [CookieConsentService::SOURCE_POPUP, CookieConsentService::SOURCE_SETTINGS_PAGE], true)
            ? $source
            : CookieConsentService::SOURCE_POPUP;
        $previousStatus = in_array($previousStatus, [CookieConsentService::STATUS_ACCEPTED, CookieConsentService::STATUS_REJECTED], true)
            ? $previousStatus
            : null;

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
            $source,
            $previousStatus
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
