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

/**
 * Layer 2 (エンドポイント単体) tests for UcpCheckoutController.
 *
 * UCP checkout の 5 エンドポイントを HTTP レベルで検証する。create→get→complete のハッピーパス、
 * 住所未確定の incomplete、他セッション遮断 (404)、Idempotency-Key リプレイ、cancel を確認する。
 *
 * @see https://github.com/EC-CUBE/ec-cube/issues/6574
 * @see https://github.com/Universal-Commerce-Protocol/ucp UCP checkout-rest.md (v2026-04-08)
 */
final class UcpCheckoutControllerTest extends EccubeTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $baseInfo = self::getContainer()->get(BaseInfoRepository::class)->get();
        $baseInfo->setUcpCheckoutEnabled(true);
        $this->entityManager->flush();
    }

    private function createPurchasableProductClass(string $stock = '100'): ProductClass
    {
        $Product = $this->createProduct('UCP チェックアウトテスト商品', 1);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()[0];
        $ProductClass->setStock($stock);
        $ProductClass->setStockUnlimited(false);
        $this->entityManager->flush();

        return $ProductClass;
    }

    /**
     * @param array<string, mixed> $body
     *
     * @return array<string, mixed>
     */
    private function postJson(string $uri, array $body, string $method = 'POST', array $server = []): array
    {
        $this->client->request($method, $uri, [], [], array_merge(['CONTENT_TYPE' => 'application/json'], $server), (string) json_encode($body));
        $response = $this->client->getResponse();

        /** @var array<string, mixed> $decoded */
        $decoded = json_decode((string) $response->getContent(), true);

        return $decoded;
    }

    /**
     * @return array<string, mixed>
     */
    private function createPayload(int $productClassId, bool $withAddress = true): array
    {
        $payload = [
            'currency' => 'JPY',
            'line_items' => [['item' => ['id' => (string) $productClassId], 'quantity' => 1]],
            'buyer' => ['first_name' => '太郎', 'last_name' => '山田', 'email' => 'ucp-agent@example.com', 'phone_number' => '0612345678'],
        ];

        if ($withAddress) {
            $payload['fulfillment'] = [
                'destinations' => [[
                    'id' => 'dest-1',
                    'last_name' => '山田',
                    'first_name' => '太郎',
                    'postal_code' => '5300001',
                    'address_region' => '大阪府',
                    'address_locality' => '大阪市北区',
                    'street_address' => '梅田1-1-1',
                    'phone_number' => '0612345678',
                ]],
            ];
        }

        return $payload;
    }

    public function testCreateGetCompleteHappyPath(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');

        $created = $this->postJson('/ucp/checkout-sessions', $this->createPayload((int) $ProductClass->getId()));
        $this->assertSame(201, $this->client->getResponse()->getStatusCode(), 'create は 201 を返す');
        $this->assertSame('2026-04-08', $created['ucp']['version'], 'UCP バージョンを広告する');
        $this->assertSame('ready_for_complete', $created['status'], '住所と在庫が揃えば ready_for_complete');
        $this->assertNotEmpty($created['id']);
        $this->assertSame('JPY', $created['currency']);

        // totals: subtotal/total が必ず1つずつ存在する (MUST)。
        $types = array_column($created['totals'], 'type');
        $this->assertSame(1, count(array_keys($types, 'subtotal', true)), 'totals は subtotal をちょうど1つ含む');
        $this->assertSame(1, count(array_keys($types, 'total', true)), 'totals は total をちょうど1つ含む');

        // links: privacy_policy / terms_of_service (法令順守のため必須)。
        $linkTypes = array_column($created['links'], 'type');
        $this->assertContains('privacy_policy', $linkTypes);
        $this->assertContains('terms_of_service', $linkTypes);

        $sessionId = $created['id'];

        // GET 取得。
        $this->client->request('GET', '/ucp/checkout-sessions/'.$sessionId);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode(), 'get は 200');

        // complete。
        $completed = $this->postJson('/ucp/checkout-sessions/'.$sessionId.'/complete', []);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode(), 'complete は 200');
        $this->assertSame('completed', $completed['status'], 'complete 後は completed');
        $this->assertArrayHasKey('order', $completed, 'complete 後は order を含む (MUST)');
        $this->assertNotEmpty($completed['order']['id']);
        $this->assertNotEmpty($completed['order']['permalink_url'], 'order.permalink_url は必須');
    }

    public function testCreateWithoutAddressIsIncomplete(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');

        $created = $this->postJson('/ucp/checkout-sessions', $this->createPayload((int) $ProductClass->getId(), withAddress: false));

        $this->assertSame(201, $this->client->getResponse()->getStatusCode());
        $this->assertSame('incomplete', $created['status'], '住所未確定なら incomplete');
        $this->assertNotEmpty($created['messages'], '住所要求のメッセージを含む');
        $this->assertSame('recoverable', $created['messages'][0]['severity'], '住所は update で再入力可能なため recoverable');
    }

    public function testGetUnknownSessionReturns404(): void
    {
        $this->client->request('GET', '/ucp/checkout-sessions/ucp_cs_does_not_exist');
        $this->assertSame(404, $this->client->getResponse()->getStatusCode(), '存在しない/他マーチャントのセッションは 404');
    }

    public function testIdempotentCreateReplaysSameSession(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $payload = $this->createPayload((int) $ProductClass->getId());

        $first = $this->postJson('/ucp/checkout-sessions', $payload, 'POST', ['HTTP_Idempotency-Key' => 'idem-key-1']);
        $second = $this->postJson('/ucp/checkout-sessions', $payload, 'POST', ['HTTP_Idempotency-Key' => 'idem-key-1']);

        $this->assertSame($first['id'], $second['id'], 'MUST NOT re-execute: 同一キーは同じセッションをリプレイする');
    }

    public function testCancel(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $created = $this->postJson('/ucp/checkout-sessions', $this->createPayload((int) $ProductClass->getId()));
        $sessionId = $created['id'];

        $canceled = $this->postJson('/ucp/checkout-sessions/'.$sessionId.'/cancel', []);
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertSame('canceled', $canceled['status'], 'cancel 後は canceled');
    }

    public function testDisabledFlagReturns404(): void
    {
        $baseInfo = self::getContainer()->get(BaseInfoRepository::class)->get();
        $baseInfo->setUcpCheckoutEnabled(false);
        $this->entityManager->flush();

        $this->client->request('GET', '/ucp/checkout-sessions/ucp_cs_anything');
        $this->assertSame(404, $this->client->getResponse()->getStatusCode(), 'ucp_checkout_enabled=false なら 404');
    }
}
