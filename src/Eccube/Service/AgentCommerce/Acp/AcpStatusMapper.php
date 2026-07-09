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

namespace Eccube\Service\AgentCommerce\Acp;

use Eccube\Entity\Master\CheckoutSessionStatus;

/**
 * EC-CUBE の正規化ステータスマスタ → ACP の checkout session status 語彙の境界変換.
 *
 * EC-CUBE はプロトコル横断の正規化ステータス (incomplete/ready/completed/canceled/expired/
 * requires_action/in_progress) をマスタで保持する。ACP は 11 値のより細かい語彙を持つため、
 * 公開境界でのみ変換する (マスタは拡張しない方針)。
 *
 * - 追加認証 (EMV-3DS) 待ちの `requires_action` は、決済ハンドラが actionData で 3DS を示した場合
 *   `authentication_required` へ、それ以外は `requires_escalation` へ振り分ける (#6777 の状態機械)。
 * - 在庫はあるが住所等が未確定で支払不能な状態は `not_ready_for_payment`、明細自体が未確定の作成直後は
 *   `incomplete` とする。
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP openapi.agentic_checkout.yaml (CheckoutSessionBase.status enum)
 */
class AcpStatusMapper
{
    /**
     * 正規化ステータスマスタ id を ACP の status 文字列へ変換する.
     *
     * @param array<string, mixed> $actionData       REQUIRES_ACTION 時の中立データ (3DS 判定に使用)
     * @param bool                 $hasBlockingError 支払を妨げるビジネス系エラー (在庫切れ・住所未確定等) があるか
     */
    public function toAcpStatus(?CheckoutSessionStatus $status, array $actionData = [], bool $hasBlockingError = false): string
    {
        return match ($status?->getId()) {
            CheckoutSessionStatus::READY => 'ready_for_payment',
            CheckoutSessionStatus::COMPLETED => 'completed',
            CheckoutSessionStatus::CANCELED => 'canceled',
            CheckoutSessionStatus::EXPIRED => 'expired',
            // 追加認証 (3DS) 待ちは authentication_required、外部ハンドオフ等は requires_escalation。
            CheckoutSessionStatus::REQUIRES_ACTION => $this->isAuthentication($actionData) ? 'authentication_required' : 'requires_escalation',
            // 非同期決済の確定待ち。
            CheckoutSessionStatus::IN_PROGRESS => 'complete_in_progress',
            // INCOMPLETE: ブロッキングエラー有りは not_ready_for_payment、無ければ作成直後の incomplete。
            default => $hasBlockingError ? 'not_ready_for_payment' : 'incomplete',
        };
    }

    /**
     * actionData が追加認証 (EMV-3DS) を要求しているか判定する.
     *
     * 決済ハンドラは 3DS チャレンジを要する場合、actionData に `intervention` = `3ds` または
     * `authentication_required` = true を載せて返す (それ以外は外部サイトへの buyer ハンドオフ等)。
     *
     * @param array<string, mixed> $actionData
     */
    private function isAuthentication(array $actionData): bool
    {
        if (($actionData['intervention'] ?? null) === '3ds' || ($actionData['type'] ?? null) === '3ds') {
            return true;
        }

        return ($actionData['authentication_required'] ?? false) === true
            || isset($actionData['authentication_metadata']);
    }
}
