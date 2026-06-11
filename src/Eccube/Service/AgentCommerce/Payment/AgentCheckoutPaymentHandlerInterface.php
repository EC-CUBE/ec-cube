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

namespace Eccube\Service\AgentCommerce\Payment;

use Eccube\Entity\Order;

/**
 * エージェントチェックアウトの決済ハンドラ共通基底.
 *
 * ACP の Shared Payment Token / UCP の Payment Token Exchange 等、プロトコル固有の
 * トークン償還 (redeem) はそれぞれの派生インターフェイス (#6776 ACP / #6574 UCP) が
 * 本基底を継承して定義する。実装は決済プラグイン側に置く (core は型のみ保持)。
 */
interface AgentCheckoutPaymentHandlerInterface
{
    /**
     * 与信 (オーソリ) を行う.
     *
     * @param array<string, mixed> $paymentData 支払トークン等の中立表現
     */
    public function authorize(Order $order, array $paymentData): void;

    /**
     * 売上確定 (キャプチャ) を行う.
     *
     * @param array<string, mixed> $paymentData 支払トークン等の中立表現
     */
    public function capture(Order $order, array $paymentData): void;

    /**
     * このハンドラが指定の支払方法 (Payment.method_class 等) を扱えるか.
     */
    public function supports(Order $order): bool;
}
