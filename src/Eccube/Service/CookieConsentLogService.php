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

/**
 * クッキー同意の操作記録（ログ）を出力するサービス。
 *
 * 同意/拒否の操作を Monolog 専用チャンネル `cookie_consent` へ出力する。
 * DB・外部ストレージには依存せず、出力はベストエフォート（失敗してもユーザー体験を妨げない）。
 *
 * 【GDPR/APPI 制限事項】本ログは IP アドレス・User-Agent・会員 ID を含むため、ログ自体が
 * 個人データ（GDPR）／個人関連情報（改正個人情報保護法）の取扱いに該当する。運用時は以下に留意すること。
 *   - 取得・保持の事実はクッキーポリシーで開示すること。
 *   - ログの保持期間はファイルログのため Monolog のローテーション設定に依存する（同意 Cookie の有効期限
 *     COOKIE_LIFETIME_DAYS=365 とは別物。ログ側に自動削除の仕組みはない）。
 *   - 退会・消去請求時に該当記録を削除する導線は現状ない（ファイルログのため）。
 *   - 拒否操作の IP まで保持するため、データ最小化の観点では将来ハッシュ化等を検討。
 *
 * @see \logs() src/Eccube/Resource/functions/log.php
 */
class CookieConsentLogService
{
    /**
     * Monolog チャンネル名
     */
    public const LOG_CHANNEL = 'cookie_consent';

    /**
     * クッキー同意ログを出力する。
     *
     * 出力失敗時でもユーザー体験を損なわないよう、ベストエフォートで処理する。
     * 失敗時はエラーログのみ記録し、例外は投げない。
     *
     * @param array{
     *     timestamp: string,
     *     consent_status: string,
     *     customer_id: int|null,
     *     session_id: string,
     *     ip_address: string,
     *     user_agent: string,
     *     source: string,
     *     previous_status: string|null
     * } $logData ログデータ
     */
    public function saveLog(array $logData): void
    {
        try {
            logs(self::LOG_CHANNEL)->info('cookie consent updated', $logData);
        } catch (\Throwable $e) {
            // ベストエフォート: 同意ログの記録に失敗してもユーザー体験を損なわないため、例外は投げない
            log_error('クッキー同意ログの記録に失敗しました', [
                'exception' => $e->getMessage(),
            ]);
        }
    }

    /**
     * ログデータを構築する。
     *
     * @param string $consentStatus 同意状態（accepted/rejected）
     * @param int|null $customerId 会員ID（ゲストは null）
     * @param string $sessionId セッションID
     * @param string $ipAddress IPアドレス
     * @param string $userAgent ユーザーエージェント
     * @param string $source 操作元（popup/settings_page）
     * @param string|null $previousStatus 変更前の状態
     *
     * @return array{
     *     timestamp: string,
     *     consent_status: string,
     *     customer_id: int|null,
     *     session_id: string,
     *     ip_address: string,
     *     user_agent: string,
     *     source: string,
     *     previous_status: string|null
     * }
     */
    public function buildLogData(
        string $consentStatus,
        ?int $customerId,
        string $sessionId,
        string $ipAddress,
        string $userAgent,
        string $source,
        ?string $previousStatus = null,
    ): array {
        return [
            'timestamp' => (new \DateTime())->format(\DateTime::ATOM),
            'consent_status' => $consentStatus,
            'customer_id' => $customerId,
            'session_id' => $sessionId,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'source' => $source,
            'previous_status' => $previousStatus,
        ];
    }
}
