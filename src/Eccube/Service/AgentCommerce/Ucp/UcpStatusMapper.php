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

namespace Eccube\Service\AgentCommerce\Ucp;

use Eccube\Entity\Master\CheckoutSessionStatus;

/**
 * EC-CUBE の正規化ステータスマスタ ↔ UCP の checkout status 語彙の境界変換.
 *
 * EC-CUBE はプロトコル横断の正規化ステータス (incomplete/ready/completed/canceled/expired) を
 * マスタで保持する。UCP の正準語彙とは `ready` ↔ `ready_for_complete` が異なるため、
 * 公開境界でのみ変換する (マスタは拡張しない方針)。
 *
 * UCP の一過性ステータス `requires_escalation` / `complete_in_progress` はマスタに永続化せず、
 * messages[] の severity から導出する ({@link UcpCheckoutSessionMapper})。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp UCP checkout.json (status enum)
 */
class UcpStatusMapper
{
    /**
     * 正規化ステータスマスタ id を UCP の status 文字列へ変換する.
     */
    public function toUcpStatus(?CheckoutSessionStatus $status): string
    {
        return match ($status?->getId()) {
            CheckoutSessionStatus::READY => 'ready_for_complete',
            CheckoutSessionStatus::COMPLETED => 'completed',
            CheckoutSessionStatus::CANCELED => 'canceled',
            // expired は UCP の正準語彙に無いため、終端の canceled として表現する。
            CheckoutSessionStatus::EXPIRED => 'canceled',
            default => 'incomplete',
        };
    }
}
