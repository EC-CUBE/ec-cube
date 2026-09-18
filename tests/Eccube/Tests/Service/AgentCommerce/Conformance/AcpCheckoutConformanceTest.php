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

use Eccube\Entity\Master\CheckoutSessionStatus;
use Eccube\Entity\ProductClass;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CheckoutSessionRepository;
use Eccube\Repository\Master\CheckoutSessionStatusRepository;
use Eccube\Service\AgentCommerce\Acp\AcpMessageMapper;
use Eccube\Tests\EccubeTestCase;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Layer 0: ACP Agentic Checkout の規範要件 (MUST / MUST NOT) を 1:1 でトレースする conformance テスト.
 *
 * 各テストは 1 規範要件に対応し、assertion の失敗メッセージに MUST 文を、docblock に出典 URL を
 * バージョン固定 (2026-04-17) で記載する。未実装の要件は markTestIncomplete で残す。
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP rfc.agentic_checkout.md (2026-04-17)
 */
final class AcpCheckoutConformanceTest extends EccubeTestCase
{
    private const TOKEN = 'acp-checkout-token';

    protected function setUp(): void
    {
        parent::setUp();
        $baseInfo = self::getContainer()->get(BaseInfoRepository::class)->get();
        $baseInfo->setAcpCheckoutEnabled(true);
        $this->entityManager->flush();
    }

    private function productClass(string $stock = '100'): ProductClass
    {
        $Product = $this->createProduct('ACP 適合性テスト商品', 1);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()[0];
        $ProductClass->setStock($stock);
        $ProductClass->setStockUnlimited(false);
        $ProductClass->getProductStock()->setStock($stock);
        $this->entityManager->flush();

        return $ProductClass;
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(int $productClassId, int $quantity = 1): array
    {
        return [
            'currency' => 'jpy',
            'line_items' => [['id' => (string) $productClassId, 'quantity' => $quantity]],
            'buyer' => ['first_name' => '太郎', 'last_name' => '山田', 'email' => 'acp@example.com', 'phone_number' => '0612345678'],
            'fulfillment_details' => [
                'name' => '山田 太郎',
                'address' => ['line_one' => '梅田1-1-1', 'city' => '大阪市北区', 'state' => '大阪府', 'country' => 'JP', 'postal_code' => '5300001'],
            ],
        ];
    }

    /**
     * @param array<string, mixed>  $body
     * @param array<string, string> $extraServer
     *
     * @return array<string, mixed>
     */
    private function post(string $uri, array $body, array $extraServer = []): array
    {
        $server = array_merge([
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.self::TOKEN,
            'HTTP_Idempotency-Key' => 'conf-'.bin2hex(random_bytes(8)),
        ], $extraServer);
        $this->client->request(Request::METHOD_POST, $uri, [], [], $server, (string) json_encode($body));

        return json_decode((string) $this->client->getResponse()->getContent(), true) ?? [];
    }

    /**
     * MUST: Idempotency-Key header is required on all POST requests (missing -> 400 idempotency_key_required).
     *
     * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP rfc.agentic_checkout.md §6.1
     */
    public function testIdempotencyKeyRequiredOnPost(): void
    {
        $pc = $this->productClass();
        $this->client->request(Request::METHOD_POST, '/acp/checkout_sessions', [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.self::TOKEN,
        ], (string) json_encode($this->payload((int) $pc->getId())));

        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode(), 'MUST: Idempotency-Key header is required on all POST requests');
        $body = json_decode((string) $this->client->getResponse()->getContent(), true);
        $this->assertSame('idempotency_key_required', $body['code'], 'code MUST be "idempotency_key_required"');
    }

    /**
     * MUST: Same key + different body -> 422 idempotency_conflict.
     *
     * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP rfc.agentic_checkout.md §6.4
     */
    public function testIdempotencyConflictReturns422(): void
    {
        $pc = $this->productClass();
        $this->post('/acp/checkout_sessions', $this->payload((int) $pc->getId(), 1), ['HTTP_Idempotency-Key' => 'conf-conflict']);
        $conflict = $this->post('/acp/checkout_sessions', $this->payload((int) $pc->getId(), 2), ['HTTP_Idempotency-Key' => 'conf-conflict']);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode(), 'MUST: same key + different body returns 422 idempotency_conflict');
        $this->assertSame('idempotency_conflict', $conflict['code']);
    }

    /**
     * MUST NOT re-execute side effects on replay; replayed response SHOULD include Idempotent-Replayed: true.
     *
     * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP rfc.agentic_checkout.md §6.3
     */
    public function testReplayDoesNotReexecuteSideEffects(): void
    {
        $pc = $this->productClass();
        $first = $this->post('/acp/checkout_sessions', $this->payload((int) $pc->getId()), ['HTTP_Idempotency-Key' => 'conf-replay']);
        $second = $this->post('/acp/checkout_sessions', $this->payload((int) $pc->getId()), ['HTTP_Idempotency-Key' => 'conf-replay']);

        $this->assertSame($first['id'], $second['id'], 'MUST NOT re-execute side effects on replay');
        $this->assertSame('true', $this->client->getResponse()->headers->get('Idempotent-Replayed'), 'replayed response SHOULD include Idempotent-Replayed: true');
    }

    /**
     * MUST: monetary amounts are integers in the currency's minor unit.
     *
     * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP rfc.agentic_checkout.md §3.1
     */
    public function testMonetaryAmountsAreMinorUnitIntegers(): void
    {
        $pc = $this->productClass();
        $created = $this->post('/acp/checkout_sessions', $this->payload((int) $pc->getId()));

        foreach ($created['totals'] as $total) {
            $this->assertIsInt($total['amount'], 'MUST: all monetary amounts are integers in minor units');
        }
    }

    /**
     * MUST: business outcomes (out of stock) are HTTP 200/201 + messages[], NOT a 4xx error.
     *
     * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP rfc.agentic_checkout.md §5
     */
    public function testBusinessOutcomeUsesMessagesNotHttpError(): void
    {
        $pc = $this->productClass('0');
        $created = $this->post('/acp/checkout_sessions', $this->payload((int) $pc->getId()));

        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode(), 'MUST: business failures are not protocol-level 4xx errors');
        $this->assertNotEmpty($created['messages'], 'MUST: business outcomes are surfaced in messages[]');
        $this->assertSame('not_ready_for_payment', $created['status']);
    }

    /**
     * MUST: when status is authentication_required and complete is called without authentication_result,
     * server returns 4XX with type=invalid_request, code=requires_3ds, param=$.authentication_result.
     *
     * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP rfc.agentic_checkout.md §4.4
     */
    public function testRequires3dsWhenAuthenticationResultMissing(): void
    {
        $pc = $this->productClass();
        $created = $this->post('/acp/checkout_sessions', $this->payload((int) $pc->getId()));

        // セッションを 3DS の認証待ち状態に遷移させる (決済ハンドラが REQUIRES_ACTION を返した状況を再現)。
        $repository = self::getContainer()->get(CheckoutSessionRepository::class);
        $statusRepository = self::getContainer()->get(CheckoutSessionStatusRepository::class);
        $session = $repository->findOneBySessionId($created['id']);
        $this->assertNotNull($session);
        $session->setStatus($statusRepository->find(CheckoutSessionStatus::REQUIRES_ACTION));
        $session->setMetadata(array_merge($session->getMetadata() ?? [], ['payment_action' => ['intervention' => '3ds']]));
        $this->entityManager->flush();

        // authentication_result を含めずに complete -> 400 requires_3ds。
        $error = $this->post('/acp/checkout_sessions/'.$created['id'].'/complete', ['payment_data' => ['handler_id' => 'card_tokenized']]);

        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode(), 'MUST: missing authentication_result returns 4XX');
        $this->assertSame('requires_3ds', $error['code'], 'code MUST be "requires_3ds"');
        $this->assertSame('$.authentication_result', $error['param'], 'param MUST be "$.authentication_result"');
    }

    /**
     * MUST: a completed or canceled session cannot be canceled (405).
     *
     * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP openapi.agentic_checkout.yaml (cancel 405)
     */
    public function testCancelCompletedReturns405(): void
    {
        $pc = $this->productClass();
        $created = $this->post('/acp/checkout_sessions', $this->payload((int) $pc->getId()));
        $this->post('/acp/checkout_sessions/'.$created['id'].'/complete', []);

        $this->post('/acp/checkout_sessions/'.$created['id'].'/cancel', []);
        $this->assertSame(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode(), 'MUST: a completed session cannot be canceled');
    }

    /**
     * Inbound markdown content MUST NOT contain raw HTML (servers MUST reject).
     *
     * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP rfc.agentic_checkout.md §5.1
     */
    public function testInboundMarkdownRejectsRawHtml(): void
    {
        $mapper = self::getContainer()->get(AcpMessageMapper::class);
        $this->expectException(\InvalidArgumentException::class);
        $mapper->assertNoRawHtml('<iframe src="evil"></iframe>');
    }

    /**
     * Get Order webhook の Merchant-Signature 検証は outbound 送出と合わせて後続 PR で実装する.
     *
     * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP openapi.agentic_checkout_webhook.yaml
     */
    #[DoesNotPerformAssertions]
    public function testWebhookDispatchDeferred(): void
    {
        $this->markTestIncomplete('Webhook の outbound 送出 (order_create/order_update) は後続 PR で実装する (署名基盤 AcpMessageSigner は本 PR に存在)。');
    }
}
