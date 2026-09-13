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

use Eccube\Entity\AgentCheckoutIdempotency;
use Eccube\Entity\Master\CheckoutSessionStatus;
use Eccube\Repository\AgentCheckoutIdempotencyRepository;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutMessage;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutMessageLevel;
use Eccube\Service\AgentCommerce\Exception\IdempotencyConflictException;
use Eccube\Service\AgentCommerce\Idempotency\AgentCheckoutIdempotencyStore;
use Eccube\Service\AgentCommerce\Ucp\UcpMessageMapper;
use Eccube\Service\AgentCommerce\Ucp\UcpStatusMapper;
use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;

/**
 * Layer 0: UCP Checkout 仕様適合性 (規範要件 1:1 トレース).
 *
 * UCP 一次仕様 (v2026-04-08) の MUST / MUST NOT を 1 テスト = 1 要件で追跡する。
 * 各 assertion の第3引数に規範要件文、docblock に出典 URL を行アンカー付きで記す。
 * HTTP レベルの規範 (エンドポイント挙動) は {@link \Eccube\Tests\Web\AgentCommerce\UcpCheckoutControllerTest}
 * が補完する。未実装の要件は markTestIncomplete で残す (実装が進むと green へ遷移)。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp UCP (pin: v2026-04-08)
 */
final class UcpCheckoutConformanceTest extends EccubeTestCase
{
    private function idempotencyStore(): AgentCheckoutIdempotencyStore
    {
        /** @var AgentCheckoutIdempotencyRepository $repository */
        $repository = $this->entityManager->getRepository(AgentCheckoutIdempotency::class);

        return new AgentCheckoutIdempotencyStore($this->entityManager, $repository);
    }

    /**
     * status は incomplete/requires_escalation/ready_for_complete/complete_in_progress/completed/canceled。
     * EC-CUBE の正規化 ready は UCP の ready_for_complete として広告されなければならない。
     *
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/source/schemas/shopping/checkout.json#L65 (status enum)
     */
    public function testReadyIsAdvertisedAsReadyForComplete(): void
    {
        $status = new CheckoutSessionStatus();
        $status->setId(CheckoutSessionStatus::READY);

        $this->assertSame('ready_for_complete', (new UcpStatusMapper())->toUcpStatus($status), 'MUST: a session whose required info is present is advertised as "ready_for_complete".');
    }

    /**
     * ビジネスロジックエラーは HTTP 200 + messages[] で表現し、error は severity を持つ
     * (recoverable / requires_buyer_input / requires_buyer_review / unrecoverable)。
     *
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/source/schemas/common/types/message_error.json#L38 (severity enum)
     */
    public function testBusinessErrorsCarrySeverity(): void
    {
        $messages = (new UcpMessageMapper())->toUcpMessages([
            new AgentCheckoutMessage(AgentCheckoutMessageLevel::ERROR, '在庫が不足しています'),
        ]);

        $this->assertSame('error', $messages[0]['type'], 'MUST: business outcomes are conveyed via messages[] with a type.');
        $this->assertContains($messages[0]['severity'], ['recoverable', 'requires_buyer_input', 'requires_buyer_review', 'unrecoverable'], 'MUST: an error message carries a severity from the defined enum.');
    }

    /**
     * 同一 Idempotency-Key が異なるパラメータで再利用された場合は 409 Conflict 相当で拒否しなければならない。
     *
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/docs/specification/checkout-rest.md (Idempotency-Key reuse -> 409)
     */
    public function testIdempotencyKeyReuseWithDifferentParamsIsRejected(): void
    {
        $store = $this->idempotencyStore();
        $store->execute('k', 'hash-A', static fn (): array => ['status' => 201, 'body' => []]);

        $this->expectException(IdempotencyConflictException::class);
        $store->execute('k', 'hash-B', static fn (): array => ['status' => 201, 'body' => []]);
    }

    /**
     * 同一 Idempotency-Key の再送はサーバ側の副作用を再実行してはならない (リプレイ)。
     *
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/docs/specification/checkout-rest.md (Idempotency-Key replay)
     */
    public function testIdempotentReplayDoesNotReexecuteSideEffects(): void
    {
        $store = $this->idempotencyStore();
        $calls = 0;
        $compute = function () use (&$calls): array {
            ++$calls;

            return ['status' => 201, 'body' => ['id' => 'cs']];
        };

        $store->execute('k', 'h', $compute);
        $store->execute('k', 'h', $compute);

        $this->assertSame(1, $calls, 'MUST NOT: a duplicate Idempotency-Key re-executes side effects.');
    }

    /**
     * status が requires_escalation のとき continue_url は絶対 HTTPS URL でなければならない。
     *
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/docs/specification/checkout.md (continue_url MUST be absolute HTTPS when requires_escalation)
     */
    #[DoesNotPerformAssertions]
    public function testContinueUrlMustBeAbsoluteHttpsOnEscalation(): void
    {
        $this->markTestIncomplete('requires_escalation の永続化と continue_url 生成は MVP では未実装 (escalation は recoverable で代替)。');
    }

    /**
     * complete (completed) 後、事業者は注文確認メールを送信しなければならない。
     *
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/docs/specification/checkout.md (confirmation email after completion)
     */
    #[DoesNotPerformAssertions]
    public function testCompleteSendsConfirmationEmail(): void
    {
        $this->markTestIncomplete('確認メール送信は shopping flow / MailService 連携で別途検証する (本 Layer では未トレース)。');
    }

    /**
     * 全エンドポイントは HTTPS (TLS 1.3+) で提供されなければならない。
     *
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/main/docs/specification/checkout-rest.md (all endpoints over TLS 1.3+)
     */
    #[DoesNotPerformAssertions]
    public function testAllEndpointsRequireHttps(): void
    {
        $this->markTestIncomplete('HTTPS 強制はトランスポート層 (デプロイ構成) の責務であり、アプリ層ではトレースしない。');
    }
}
