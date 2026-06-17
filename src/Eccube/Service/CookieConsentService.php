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

namespace Eccube\Service;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * クッキー同意状態を管理するサービス。
 *
 * 同意状態の真実は Cookie に一元化し、本サービスは Cookie の読み書きと状態判定のみを担う。
 * 同意 Cookie はサーバ側レスポンス（Set-Cookie）でのみ設定し、Secure 属性等は Request::isSecure() に統一する。
 */
class CookieConsentService
{
    /**
     * Cookie名
     */
    public const COOKIE_NAME = 'eccube_cookie_consent';

    /**
     * 同意状態: 同意済み
     */
    public const STATUS_ACCEPTED = 'accepted';

    /**
     * 同意状態: 拒否
     */
    public const STATUS_REJECTED = 'rejected';

    /**
     * Cookie有効期限（日数）
     */
    private const COOKIE_LIFETIME_DAYS = 365;

    /**
     * 現在の同意状態を取得する。
     *
     * @param Request $request リクエストオブジェクト
     *
     * @return string|null 同意状態（accepted/rejected）または未設定（null）
     */
    public function getConsentStatus(Request $request): ?string
    {
        return $request->cookies->get(self::COOKIE_NAME);
    }

    /**
     * 同意が得られているか（同意または拒否のいずれか）をチェックする。
     *
     * @param Request $request リクエストオブジェクト
     *
     * @return bool true: 同意/拒否のいずれかが選択済み、false: 未設定
     */
    public function isConsentGiven(Request $request): bool
    {
        $status = $this->getConsentStatus($request);

        return $status === self::STATUS_ACCEPTED || $status === self::STATUS_REJECTED;
    }

    /**
     * 同意されているかチェックする。
     *
     * @param Request $request リクエストオブジェクト
     *
     * @return bool true: 同意済み、false: 拒否または未設定
     */
    public function isConsentAccepted(Request $request): bool
    {
        return $this->getConsentStatus($request) === self::STATUS_ACCEPTED;
    }

    /**
     * 同意状態を保存する（Response に Set-Cookie を設定）。
     *
     * @param Response $response レスポンスオブジェクト
     * @param string $status 同意状態（accepted/rejected）
     * @param Request $request リクエストオブジェクト（Secure 判定用）
     */
    public function saveConsentStatus(Response $response, string $status, Request $request): void
    {
        // 有効期限を計算（現在時刻 + 365日）
        $expireTime = time() + (self::COOKIE_LIFETIME_DAYS * 86400);

        $cookie = Cookie::create(
            self::COOKIE_NAME,
            $status,
            $expireTime,
            '/',
            null,
            $request->isSecure(), // HTTPS 環境でのみ Secure=true
            false, // HttpOnly=false（JavaScript から参照するため）
            false,
            Cookie::SAMESITE_LAX
        );

        $response->headers->setCookie($cookie);
    }
}
