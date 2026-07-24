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

namespace Eccube\Tests\Service\AgentCommerce\Conformance;

use Eccube\Entity\Master\AgentProtocol;
use Eccube\Entity\Master\CheckoutSessionStatus;
use Eccube\Entity\Order;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutMessageLevel;
use Eccube\Service\AgentCommerce\Payment\PaymentOutcomeStatus;
use PHPUnit\Framework\TestCase;

/**
 * Layer 0 (conformance) tests for the CheckoutSession 中核 (#6777 Phase 1b).
 *
 * ACP/UCP 横断の規範要件のうち、共通基盤の CheckoutSession 中核が担保するものを 1:1 でトレースする。
 * 中核で実装できない要件 (controller での HTTP 変換等) は markTestIncomplete で残す。
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol/tree/main/spec/2026-04-17
 * @see https://github.com/Universal-Commerce-Protocol/ucp
 */
final class AgentCheckoutCoreConformanceTest extends TestCase
{
    /**
     * CheckoutSession.status は ACP/UCP 横断の正規化ステータスマスタであり、`canceled` を含む.
     *
     * (incomplete=1 / ready=2 / completed=3 / canceled=4 / expired=5 /
     *  requires_action=6 / in_progress=7)
     * 正準名 (`canceled` 等) は import_csv で seed され、DB 上の値は Layer 2 で検証する。
     */
    public function testNormalizedStatusIncludesCanceled(): void
    {
        $ids = [
            CheckoutSessionStatus::INCOMPLETE,
            CheckoutSessionStatus::READY,
            CheckoutSessionStatus::COMPLETED,
            CheckoutSessionStatus::CANCELED,
            CheckoutSessionStatus::EXPIRED,
            CheckoutSessionStatus::REQUIRES_ACTION,
            CheckoutSessionStatus::IN_PROGRESS,
        ];

        $this->assertContains(CheckoutSessionStatus::CANCELED, $ids, 'MUST: 正規化ステータスマスタは canceled を含む');
        $this->assertSame([1, 2, 3, 4, 5, 6, 7], $ids, '正規化ステータスマスタの定数が 7 段そろう');
    }

    /**
     * 追加認証 (3DS) / escalation はエラーでなく complete が返す中間状態であり、正規化ステータスとして表現する.
     *
     * UCP `requires_escalation` / ACP `authentication_required` → `requires_action`、
     * `complete_in_progress` → `in_progress` に正規化する (#6777)。これらは在庫を引当のまま保持し
     * 再開 complete を待つ非終端ステータスである。
     */
    public function testNormalizedStatusIncludesIntermediateActionStates(): void
    {
        $this->assertSame(6, CheckoutSessionStatus::REQUIRES_ACTION, 'MUST: 追加認証/escalation 待ちの正規化ステータス requires_action を持つ');
        $this->assertSame(7, CheckoutSessionStatus::IN_PROGRESS, 'MUST: 非同期決済確定待ちの正規化ステータス in_progress を持つ');
    }

    /**
     * 決済ハンドラの結果型は「中断→再開」状態機械を表現する 4 値を持つ.
     *
     * COMPLETED / REQUIRES_ACTION (3DS/escalation) / PENDING (非同期) / FAILED。
     * 追加認証はエラー (FAILED) ではなく REQUIRES_ACTION で表現される点が要。
     */
    public function testPaymentOutcomeCoversStateMachineSignals(): void
    {
        $values = array_map(static fn (PaymentOutcomeStatus $s): string => $s->value, PaymentOutcomeStatus::cases());

        $this->assertEqualsCanonicalizing(
            ['completed', 'requires_action', 'pending', 'failed'],
            $values,
            'MUST: 決済結果型は completed/requires_action/pending/failed の 4 状態を表現できる',
        );
    }

    /**
     * ビジネス系の結果 (HTTP 200 + messages[]) は error/warning/info の 3 段で表現する.
     *
     * @see ACP MessageError / MessageWarning / MessageInfo
     */
    public function testBusinessMessageLevelsCoverErrorWarningInfo(): void
    {
        $levels = array_map(static fn (AgentCheckoutMessageLevel $l): string => $l->value, AgentCheckoutMessageLevel::cases());

        $this->assertEqualsCanonicalizing(['error', 'warning', 'info'], $levels, 'MUST: ビジネス系メッセージは error/warning/info の 3 段を持つ');
    }

    /**
     * エージェント経由の注文は Order に protocol/agent 識別子を保持でき、通常購入では NULL である.
     */
    public function testOrderCarriesAgentAttributionAndDefaultsNull(): void
    {
        $normal = new Order();
        $this->assertNotInstanceOf(AgentProtocol::class, $normal->getAgentProtocol(), 'MUST: 通常購入の Order は agent_protocol が NULL');
        $this->assertNull($normal->getAgentId(), 'MUST: 通常購入の Order は agent_id が NULL');

        $protocol = new AgentProtocol();
        $agentOrder = new Order();
        $agentOrder->setAgentProtocol($protocol)->setAgentId('agent-1');
        $this->assertSame($protocol, $agentOrder->getAgentProtocol(), 'エージェント注文は protocol マスタを保持できる');
    }

    /**
     * プロトコル系 (HTTP 4xx/5xx) とビジネス系 (HTTP 200 + messages[]) の 2 系統への
     * 実際の HTTP 変換は checkout controller (#6776/#6574) の責務であり中核の対象外.
     */
    public function testTwoTierHttpMappingIsDeferredToControllerLayer(): void
    {
        self::markTestIncomplete('HTTP ステータスと messages[] への 2 系統変換は ACP/UCP checkout controller (#6776/#6574) で検証する。');
    }

    /**
     * リプレイ (Idempotency-Key) で副作用を再実行しない不変条件は、controller/middleware の
     * 冪等性処理に依存するため中核では未実装.
     */
    public function testIdempotentReplayIsDeferredToControllerLayer(): void
    {
        self::markTestIncomplete('Idempotency-Key によるリプレイ抑止は checkout controller (#6776/#6574) で検証する。');
    }
}
