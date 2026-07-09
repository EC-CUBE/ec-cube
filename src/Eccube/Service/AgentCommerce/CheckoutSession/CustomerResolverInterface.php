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

namespace Eccube\Service\AgentCommerce\CheckoutSession;

use Eccube\Entity\CheckoutSession;
use Eccube\Entity\Customer;

/**
 * チェックアウトセッションに紐づく会員 (Customer) を解決する seam.
 *
 * エージェントはブラウザセッション (form_login) を持たないため、会員 ID 連携は
 * 「OAuth2 トークン → Customer 解決」で行う。会員 ID 連携の実体は eccube-api4 の
 * Customer authorization_code grant に依存する ([EC-CUBE/eccube-api4#189]) ため、
 * 標準実装 ({@link GuestCustomerResolver}) は常に null (ゲスト購入) を返す。
 *
 * 会員解決を有効化する場合は本インターフェイスの実装を `app/Customize` 等で差し替える。
 */
interface CustomerResolverInterface
{
    /**
     * セッションから会員を解決する. ゲスト購入の場合は null.
     */
    public function resolve(CheckoutSession $session): ?Customer;
}
