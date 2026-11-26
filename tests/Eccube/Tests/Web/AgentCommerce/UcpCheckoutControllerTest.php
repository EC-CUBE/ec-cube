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

use Eccube\Entity\CheckoutSession;
use Eccube\Entity\Order;
use Eccube\Entity\ProductClass;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\CheckoutSessionRepository;
use Eccube\Service\AgentCommerce\Payment\AgentCheckoutPaymentHandlerRegistry;
use Eccube\Service\AgentCommerce\Payment\PaymentOutcome;
use Eccube\Service\AgentCommerce\Payment\UcpPaymentHandlerInterface;
use Eccube\Service\Payment\Method\CreditCard;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
    /** 契約違反ハンドラの handler_id (匿名クラスから参照するため public)。 */
    public const THROWING_HANDLER_ID = 'test.ucp.throwing';

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
        $decoded = json_decode((string) $response->getContent(), true) ?? [];

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
        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode(), 'create は 201 を返す');
        $this->assertSame('2026-04-08', $created['ucp']['version'], 'UCP バージョンを広告する');
        $this->assertSame('ready_for_complete', $created['status'], '住所と在庫が揃えば ready_for_complete');
        $this->assertNotEmpty($created['id']);
        $this->assertSame('JPY', $created['currency']);

        // totals: subtotal/total が必ず1つずつ存在する (MUST)。
        $types = array_column($created['totals'], 'type');
        $this->assertCount(1, array_keys($types, 'subtotal', true), 'totals は subtotal をちょうど1つ含む');
        $this->assertCount(1, array_keys($types, 'total', true), 'totals は total をちょうど1つ含む');

        // links: privacy_policy / terms_of_service (法令順守のため必須)。
        $linkTypes = array_column($created['links'], 'type');
        $this->assertContains('privacy_policy', $linkTypes);
        $this->assertContains('terms_of_service', $linkTypes);

        $sessionId = $created['id'];

        // GET 取得。
        $this->client->request(Request::METHOD_GET, '/ucp/checkout-sessions/'.$sessionId);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), 'get は 200');

        // complete。
        $completed = $this->postJson('/ucp/checkout-sessions/'.$sessionId.'/complete', []);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), 'complete は 200');
        $this->assertSame('completed', $completed['status'], 'complete 後は completed');
        $this->assertArrayHasKey('order', $completed, 'complete 後は order を含む (MUST)');
        $this->assertNotEmpty($completed['order']['id']);
        $this->assertNotEmpty($completed['order']['permalink_url'], 'order.permalink_url は必須');
    }

    /**
     * exchangePaymentToken() が契約に反して例外を投げても HTTP 500 にせずビジネス系エラーへ写像する.
     *
     * 本メソッドは complete の状態機械の**外側** (controller のペイロード解決時) で呼ばれるため、
     * {@link \Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutCompletionService} が
     * authorize/capture に対して持つ例外の砦が効かない。
     */
    public function testPaymentTokenExchangeExceptionIsMappedToBusinessError(): void
    {
        // 決済ハンドラのレジストリを差し替えるため、リクエスト毎の kernel 再起動を止める。
        $this->client->disableReboot();
        self::getContainer()->set(
            AgentCheckoutPaymentHandlerRegistry::class,
            new AgentCheckoutPaymentHandlerRegistry([$this->throwingUcpHandler()]),
        );

        $ProductClass = $this->createPurchasableProductClass('100');
        $created = $this->postJson('/ucp/checkout-sessions', $this->createPayload((int) $ProductClass->getId()));

        $completed = $this->postJson('/ucp/checkout-sessions/'.$created['id'].'/complete', [
            'payment' => ['instruments' => [['handler_id' => self::THROWING_HANDLER_ID, 'credential' => ['token' => 'tok_broken']]]],
        ]);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), 'ハンドラの例外を HTTP 500 にしない');
        $this->assertNotSame('completed', $completed['status'], '支払データを解決できないセッションは確定しない');
        $this->assertNotEmpty($completed['messages'] ?? [], 'ビジネス系エラーとして messages[] を返す');
        $this->assertNotSame('', $completed['messages'][0]['content'] ?? '', 'メッセージ本文が空でない');
        $this->assertSame('error', $completed['messages'][0]['type'] ?? null);
    }

    /**
     * exchangePaymentToken() で例外を投げる UCP 決済ハンドラ (契約違反のシミュレート).
     */
    private function throwingUcpHandler(): UcpPaymentHandlerInterface
    {
        return new class implements UcpPaymentHandlerInterface {
            public function getHandlerId(): string
            {
                return UcpCheckoutControllerTest::THROWING_HANDLER_ID;
            }

            public function exchangePaymentToken(array $credential): array
            {
                throw new \RuntimeException('PSP tokenization endpoint is unreachable.');
            }

            public function authorize(Order $order, array $paymentData, array $paymentReference = []): PaymentOutcome
            {
                return PaymentOutcome::completed('should_not_be_reached');
            }

            public function capture(Order $order, array $paymentData, PaymentOutcome $authorization): PaymentOutcome
            {
                return PaymentOutcome::completed('should_not_be_reached');
            }

            public function supports(Order $order): bool
            {
                return true;
            }
        };
    }

    public function testCreateWithoutAddressIsIncomplete(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');

        $created = $this->postJson('/ucp/checkout-sessions', $this->createPayload((int) $ProductClass->getId(), withAddress: false));

        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
        $this->assertSame('incomplete', $created['status'], '住所未確定なら incomplete');
        $this->assertNotEmpty($created['messages'], '住所要求のメッセージを含む');
        $this->assertSame('recoverable', $created['messages'][0]['severity'], '住所は update で再入力可能なため recoverable');
    }

    public function testGetUnknownSessionReturns404(): void
    {
        $this->client->request(Request::METHOD_GET, '/ucp/checkout-sessions/ucp_cs_does_not_exist');
        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), '存在しない/他マーチャントのセッションは 404');
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
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
        $this->assertSame('canceled', $canceled['status'], 'cancel 後は canceled');
    }

    /**
     * 報告の再現手順: ハンドラの無いカード決済が割り当たったセッションへ、署名なし・本文 {} で complete しても確定しない.
     */
    public function testHandlerlessCardPaymentIsNotCompletedWithoutHandlerId(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $created = $this->postJson('/ucp/checkout-sessions', $this->createPayload((int) $ProductClass->getId()));
        $this->assertSame('ready_for_complete', $created['status']);

        // 決済プラグインのクレジットカード決済 (CreditCard 継承・エージェント決済ハンドラ無し) が割り当たった状態にする。
        $order = $this->findOrderOfSession($created['id']);
        $order->getPayment()?->setMethodClass(CreditCard::class);
        $this->entityManager->flush();

        $completed = $this->postJson('/ucp/checkout-sessions/'.$created['id'].'/complete', []);

        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
        $this->assertNotSame('completed', $completed['status'], '決済処理を通らないまま確定しない');
        $this->assertArrayNotHasKey('order', $completed);
        $this->assertSame('error', $completed['messages'][0]['type'] ?? null);
    }

    /**
     * 都道府県名として解決できない region では送料を計算できないため、ready にせず確定もさせない.
     */
    public function testUnresolvableRegionIsIncompleteAndCannotBeCompleted(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $payload = $this->createPayload((int) $ProductClass->getId());
        $payload['fulfillment']['destinations'][0]['address_region'] = 'Osaka';

        $created = $this->postJson('/ucp/checkout-sessions', $payload);
        $this->assertSame('incomplete', $created['status'], '都道府県を解決できない住所は incomplete');
        $this->assertNotEmpty($created['messages']);

        $this->postJson('/ucp/checkout-sessions/'.$created['id'].'/complete', []);
        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode(), 'incomplete は確定できない');
    }

    /**
     * ready のセッションを別商品・不備のある住所で update したら、以前の見積の Order を残さない.
     *
     * 残すと、update の応答 (新しい商品の暫定見積) と GET の応答 (以前の商品・合計) が食い違う。
     */
    public function testInvalidAddressUpdateDetachesPreviousOrder(): void
    {
        $first = $this->createPurchasableProductClass('100');
        $second = $this->createPurchasableProductClass('100');
        $created = $this->postJson('/ucp/checkout-sessions', $this->createPayload((int) $first->getId()));
        $this->assertSame('ready_for_complete', $created['status']);

        $payload = $this->createPayload((int) $second->getId());
        $payload['fulfillment']['destinations'][0]['address_region'] = 'Osaka';
        $updated = $this->postJson('/ucp/checkout-sessions/'.$created['id'], $payload, 'PUT');
        $this->assertSame('incomplete', $updated['status']);

        $this->client->request(Request::METHOD_GET, '/ucp/checkout-sessions/'.$created['id']);
        $got = json_decode((string) $this->client->getResponse()->getContent(), true);
        $this->assertSame('incomplete', $got['status']);
        $this->assertSame([(string) $second->getId()], array_column($got['line_items'], 'id'), 'GET も update 後の明細を返す');
    }

    /**
     * ready のセッションを別商品・住所なしで update した場合も、以前の見積の Order を残さない.
     */
    public function testUpdateWithoutAddressDetachesPreviousOrder(): void
    {
        $first = $this->createPurchasableProductClass('100');
        $second = $this->createPurchasableProductClass('100');
        $created = $this->postJson('/ucp/checkout-sessions', $this->createPayload((int) $first->getId()));
        $this->assertSame('ready_for_complete', $created['status']);

        $updated = $this->postJson('/ucp/checkout-sessions/'.$created['id'], $this->createPayload((int) $second->getId(), withAddress: false), 'PUT');
        $this->assertSame('incomplete', $updated['status']);

        $this->client->request(Request::METHOD_GET, '/ucp/checkout-sessions/'.$created['id']);
        $got = json_decode((string) $this->client->getResponse()->getContent(), true);
        $this->assertSame('incomplete', $got['status']);
        $this->assertSame([(string) $second->getId()], array_column($got['line_items'], 'id'), 'GET も update 後の明細を返す');
    }

    public function testMissingBuyerEmailIsIncomplete(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $payload = $this->createPayload((int) $ProductClass->getId());
        unset($payload['buyer']['email']);

        $created = $this->postJson('/ucp/checkout-sessions', $payload);
        $this->assertSame('incomplete', $created['status'], 'メールアドレスの無い購入者は incomplete');
    }

    public function testHyphenatedPostalCodeIsNormalized(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $payload = $this->createPayload((int) $ProductClass->getId());
        $payload['fulfillment']['destinations'][0]['postal_code'] = '530-0001';

        $created = $this->postJson('/ucp/checkout-sessions', $payload);
        $this->assertSame('ready_for_complete', $created['status'], 'ハイフン付きの郵便番号は店頭と同様に吸収する');
    }

    public function testUnsupportedCurrencyIsRejected(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $payload = $this->createPayload((int) $ProductClass->getId());
        $payload['currency'] = 'USD';

        $error = $this->postJson('/ucp/checkout-sessions', $payload);
        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode(), '店舗の通貨以外は受け付けない');
        $this->assertSame('unsupported_currency', $error['code']);
    }

    /**
     * 確定済みのセッションは update で見積を作り直せない (確定済み受注との紐付けが外れるため).
     */
    public function testCompletedSessionCannotBeUpdated(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $payload = $this->createPayload((int) $ProductClass->getId());
        $created = $this->postJson('/ucp/checkout-sessions', $payload);
        $completed = $this->postJson('/ucp/checkout-sessions/'.$created['id'].'/complete', []);
        $this->assertSame('completed', $completed['status']);

        $this->postJson('/ucp/checkout-sessions/'.$created['id'], $payload, 'PUT');
        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());

        $this->client->request(Request::METHOD_GET, '/ucp/checkout-sessions/'.$created['id']);
        $after = json_decode((string) $this->client->getResponse()->getContent(), true);
        $this->assertSame('completed', $after['status'], 'completed のまま');
        $this->assertArrayHasKey('order', $after);
    }

    /**
     * 署名付きで作成された (エージェントに束縛された) セッションは、未署名のリクエストからは見えない.
     */
    public function testAgentBoundSessionIsHiddenFromUnsignedRequests(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $created = $this->postJson('/ucp/checkout-sessions', $this->createPayload((int) $ProductClass->getId()));

        // 署名を検証できたエージェントが作成したセッション相当にする。
        $session = self::getContainer()->get(CheckoutSessionRepository::class)->findOneBySessionId($created['id']);
        $this->assertInstanceOf(CheckoutSession::class, $session);
        $session->setAgentId('https://agent.example/.well-known/ucp');
        $this->entityManager->flush();

        $this->client->request(Request::METHOD_GET, '/ucp/checkout-sessions/'.$created['id'], [], [], [
            'HTTP_UCP_AGENT' => 'profile="https://agent.example/.well-known/ucp"',
        ]);
        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), '未署名の UCP-Agent ヘッダでは名乗れない (購入者情報を返さない)');

        $this->postJson('/ucp/checkout-sessions/'.$created['id'].'/complete', []);
        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), 'complete も遮断する');
    }

    /**
     * 未署名の UCP-Agent ヘッダ値は受注の agent_id に記録しない (誰でも名乗れるため).
     */
    public function testUnsignedAgentHeaderIsNotRecordedOnOrder(): void
    {
        $ProductClass = $this->createPurchasableProductClass('100');
        $created = $this->postJson('/ucp/checkout-sessions', $this->createPayload((int) $ProductClass->getId()), 'POST', [
            'HTTP_UCP_AGENT' => 'profile="https://spoofed.example/.well-known/ucp"',
        ]);

        $this->assertNull($this->findOrderOfSession($created['id'])->getAgentId());
    }

    private function findOrderOfSession(string $sessionId): Order
    {
        $session = self::getContainer()->get(CheckoutSessionRepository::class)->findOneBySessionId($sessionId);
        $this->assertInstanceOf(CheckoutSession::class, $session);
        $order = $session->getOrder();
        $this->assertInstanceOf(Order::class, $order);

        return $order;
    }

    public function testDisabledFlagReturns404(): void
    {
        $baseInfo = self::getContainer()->get(BaseInfoRepository::class)->get();
        $baseInfo->setUcpCheckoutEnabled(false);
        $this->entityManager->flush();

        $this->client->request(Request::METHOD_GET, '/ucp/checkout-sessions/ucp_cs_anything');
        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), 'ucp_checkout_enabled=false なら 404');
    }
}
