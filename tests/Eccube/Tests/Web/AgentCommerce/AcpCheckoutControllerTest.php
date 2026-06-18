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

namespace Eccube\Tests\Web\AgentCommerce;

use Eccube\Entity\ProductClass;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Layer 2 (エンドポイント単体) tests for AcpCheckoutController.
 *
 * ACP checkout の 5 エンドポイントを HTTP レベルで検証する。create→get→complete のハッピーパス、
 * 住所未確定、他セッション遮断 (404)、Idempotency (必須/リプレイ/異内容 422)、cancel、
 * OAuth2 認証 (欠落 401 / scope 不足 403)、フラグ無効 404 を確認する。
 *
 * @see https://github.com/EC-CUBE/ec-cube/issues/6776
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP openapi.agentic_checkout.yaml (2026-04-17)
 */
final class AcpCheckoutControllerTest extends EccubeTestCase
{
    private const TOKEN = 'acp-checkout-token';

    protected function setUp(): void
    {
        parent::setUp();

        $baseInfo = self::getContainer()->get(BaseInfoRepository::class)->get();
        $baseInfo->setAcpCheckoutEnabled(true);
        $this->entityManager->flush();
    }

    private function createPurchasableProductClass(string $stock = '100'): ProductClass
    {
        $Product = $this->createProduct('ACP チェックアウトテスト商品', 1);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()[0];
        $ProductClass->setStock($stock);
        $ProductClass->setStockUnlimited(false);
        $ProductClass->getProductStock()->setStock($stock);
        $this->entityManager->flush();

        return $ProductClass;
    }

    /**
     * @param array<string, mixed> $body
     * @param array<string, string> $server 追加サーバパラメータ (Idempotency-Key 等)
     *
     * @return array<string, mixed>
     */
    private function postJson(string $uri, array $body, array $server = [], bool $auth = true): array
    {
        $headers = ['CONTENT_TYPE' => 'application/json'];
        if ($auth) {
            $headers['HTTP_AUTHORIZATION'] = 'Bearer '.self::TOKEN;
        }
        // Idempotency-Key は POST で必須。明示指定が無ければ一意キーを自動付与する。
        if (!isset($server['HTTP_Idempotency-Key'])) {
            $server['HTTP_Idempotency-Key'] = 'idem-'.bin2hex(random_bytes(8));
        }

        $this->client->request(Request::METHOD_POST, $uri, [], [], array_merge($headers, $server), (string) json_encode($body));

        /** @var array<string, mixed> $decoded */
        $decoded = json_decode((string) $this->client->getResponse()->getContent(), true) ?? [];

        return $decoded;
    }

    /**
     * @return array<string, mixed>
     */
    private function getJson(string $uri, bool $auth = true): array
    {
        $server = $auth ? ['HTTP_AUTHORIZATION' => 'Bearer '.self::TOKEN] : [];
        $this->client->request(Request::METHOD_GET, $uri, [], [], $server);

        /** @var array<string, mixed> $decoded */
        $decoded = json_decode((string) $this->client->getResponse()->getContent(), true) ?? [];

        return $decoded;
    }

    /**
     * @return array<string, mixed>
     */
    private function createPayload(int $productClassId, bool $withAddress = true): array
    {
        $payload = [
            'currency' => 'jpy',
            'line_items' => [['id' => (string) $productClassId, 'quantity' => 1]],
            'buyer' => ['first_name' => '太郎', 'last_name' => '山田', 'email' => 'acp-agent@example.com', 'phone_number' => '0612345678'],
        ];

        if ($withAddress) {
            $payload['fulfillment_details'] = [
                'name' => '山田 太郎',
                'phone_number' => '0612345678',
                'email' => 'acp-agent@example.com',
                'address' => [
                    'name' => '山田 太郎',
                    'line_one' => '梅田1-1-1',
                    'line_two' => '',
                    'city' => '大阪市北区',
                    'state' => '大阪府',
                    'country' => 'JP',
                    'postal_code' => '5300001',
                ],
            ];
        }

        return $payload;
    }

    public function testCreateGetCompleteHappyPath(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');

        $created = $this->postJson('/acp/checkout_sessions', $this->createPayload((int) $ProductClass->getId()));
        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
        $this->assertSame('2026-04-17', $created['protocol']['version'], 'ACP バージョンを広告する');
        $this->assertSame('ready_for_payment', $created['status'], '住所と在庫が揃えば ready_for_payment');
        $this->assertSame('jpy', $created['currency'], 'currency は小文字 ISO 4217');
        $this->assertNotEmpty($created['id']);

        // totals: subtotal/total が必ず存在し display_text を持つ (ACP MUST)。
        $types = array_column($created['totals'], 'type');
        $this->assertContains('subtotal', $types);
        $this->assertContains('total', $types);
        $this->assertArrayHasKey('display_text', $created['totals'][0], 'Total は display_text 必須');

        $sessionId = $created['id'];

        $got = $this->getJson('/acp/checkout_sessions/'.$sessionId);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), 'get は 200');
        $this->assertSame($sessionId, $got['id']);

        $completed = $this->postJson('/acp/checkout_sessions/'.$sessionId.'/complete', []);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
        $this->assertSame('completed', $completed['status'], 'complete 後は completed');
        $this->assertArrayHasKey('order', $completed, 'complete 後は order を含む');
        $this->assertNotEmpty($completed['order']['id']);
        $this->assertSame($sessionId, $completed['order']['checkout_session_id']);
    }

    public function testCreateWithoutAddressIsNotReadyForPayment(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');

        $created = $this->postJson('/acp/checkout_sessions', $this->createPayload((int) $ProductClass->getId(), withAddress: false));

        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
        $this->assertSame('not_ready_for_payment', $created['status'], '住所未確定 (ブロッキングエラー) は not_ready_for_payment');
        $this->assertNotEmpty($created['messages'], '住所要求のメッセージを含む');
    }

    public function testGetUnknownSessionReturns404(): void
    {
        $this->getJson('/acp/checkout_sessions/acp_cs_does_not_exist');
        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), '存在しない/他マーチャントのセッションは 404');
    }

    public function testMissingIdempotencyKeyReturns400(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $headers = ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer '.self::TOKEN];
        $this->client->request(Request::METHOD_POST, '/acp/checkout_sessions', [], [], $headers, (string) json_encode($this->createPayload((int) $ProductClass->getId())));

        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode(), 'Idempotency-Key 欠落 POST は 400 (MUST)');
        $body = json_decode((string) $this->client->getResponse()->getContent(), true);
        $this->assertSame('idempotency_key_required', $body['code']);
    }

    public function testIdempotentCreateReplaysSameSession(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $payload = $this->createPayload((int) $ProductClass->getId());

        $first = $this->postJson('/acp/checkout_sessions', $payload, ['HTTP_Idempotency-Key' => 'idem-acp-1']);
        $second = $this->postJson('/acp/checkout_sessions', $payload, ['HTTP_Idempotency-Key' => 'idem-acp-1']);

        $this->assertSame($first['id'], $second['id'], 'MUST NOT re-execute: 同一キー+同一内容は同じセッションをリプレイ');
        $this->assertSame('true', $this->client->getResponse()->headers->get('Idempotent-Replayed'), 'リプレイは Idempotent-Replayed: true を付す (SHOULD)');
    }

    public function testIdempotencyConflictReturns422(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');

        $this->postJson('/acp/checkout_sessions', $this->createPayload((int) $ProductClass->getId()), ['HTTP_Idempotency-Key' => 'idem-acp-2']);
        // 同一キーで異なる内容 (数量変更) → 422 idempotency_conflict (MUST)。
        $payload2 = $this->createPayload((int) $ProductClass->getId());
        $payload2['line_items'][0]['quantity'] = 2;
        $conflict = $this->postJson('/acp/checkout_sessions', $payload2, ['HTTP_Idempotency-Key' => 'idem-acp-2']);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode(), '同一キー+異内容は 422');
        $this->assertSame('idempotency_conflict', $conflict['code']);
    }

    public function testCancel(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $created = $this->postJson('/acp/checkout_sessions', $this->createPayload((int) $ProductClass->getId()));

        $canceled = $this->postJson('/acp/checkout_sessions/'.$created['id'].'/cancel', []);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
        $this->assertSame('canceled', $canceled['status'], 'cancel 後は canceled');
    }

    public function testCancelCompletedSessionReturns405(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $created = $this->postJson('/acp/checkout_sessions', $this->createPayload((int) $ProductClass->getId()));
        $this->postJson('/acp/checkout_sessions/'.$created['id'].'/complete', []);

        $result = $this->postJson('/acp/checkout_sessions/'.$created['id'].'/cancel', []);
        $this->assertSame(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode(), '完了済セッションの cancel は 405');
        $this->assertSame('not_allowed', $result['code']);
    }

    public function testMissingBearerReturns401(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $this->postJson('/acp/checkout_sessions', $this->createPayload((int) $ProductClass->getId()), [], auth: false);

        $this->assertSame(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode(), 'Bearer トークン欠落は 401');
    }

    public function testInsufficientScopeReturns403(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        // ucp:checkout のみのトークンで acp:checkout を要求 → protocol 越境で 403。
        $this->postJson('/acp/checkout_sessions', $this->createPayload((int) $ProductClass->getId()), ['HTTP_AUTHORIZATION' => 'Bearer ucp-checkout-token'], auth: false);

        $this->assertSame(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode(), 'scope 不足・protocol 越境は 403');
    }

    public function testDisabledFlagReturns404(): void
    {
        $baseInfo = self::getContainer()->get(BaseInfoRepository::class)->get();
        $baseInfo->setAcpCheckoutEnabled(false);
        $this->entityManager->flush();

        $this->getJson('/acp/checkout_sessions/acp_cs_anything');
        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), 'acp_checkout_enabled=false なら 404');
    }
}
