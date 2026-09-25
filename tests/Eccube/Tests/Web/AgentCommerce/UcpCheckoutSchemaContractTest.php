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
use Eccube\Tests\Service\AgentCommerce\Schema\SchemaValidatorTrait;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Layer 2 (スキーマ契約): UCP checkout の各エンドポイントが実際に配信する生 JSON を、
 * UCP 公式 schema shopping/checkout.json (v2026-04-08) で機械検証する.
 *
 * ライフサイクル (create 住所未確定 → update で ready_for_complete → get → complete → cancel) と
 * ビジネスエラー (在庫超過 = HTTP 200 + messages[]) の応答を対象にする.
 * 散文の MUST に現れない構造制約 (required / enum / oneOf) は schema でしか表現されないため、
 * UcpCheckoutControllerTest の手書き断言とは別に schema そのもので検証する.
 *
 * 既知の未充足要件 (本ファイル末尾の markTestIncomplete 参照) は assertConformsExceptKnownGaps() で
 * 補填してから検証する. 補填は「当該要件がまだ未充足であること」を先に断言するため、修正が landing
 * すると意図的に失敗し、補填の撤去と incomplete テストの有効化を促す.
 *
 * schema 未取得環境では markTestSkipped (取得手順は tests/Eccube/Tests/Service/AgentCommerce/README.md).
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/v2026-04-08/source/schemas/shopping/checkout.json#L8 (required: ucp, id, line_items, status, currency, totals, links)
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/v2026-04-08/source/schemas/ucp.json#L213 ($defs/response_checkout_schema: payment_handlers 必須)
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/v2026-04-08/source/schemas/shopping/types/message_error.json#L6 (required: type, code, content, severity)
 * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/v2026-04-08/source/schemas/shopping/types/message_warning.json#L6 (required: type, code, content)
 */
final class UcpCheckoutSchemaContractTest extends EccubeTestCase
{
    use SchemaValidatorTrait;

    private const CHECKOUT_SCHEMA_REF = 'https://ucp.dev/schemas/shopping/checkout.json';

    /**
     * 既知ギャップ補填時に messages[].code へ入れる仮の値 (freeform code は仕様上許容される).
     */
    private const PLACEHOLDER_MESSAGE_CODE = 'eccube_known_gap_placeholder';

    protected function setUp(): void
    {
        parent::setUp();

        $baseInfo = self::getContainer()->get(BaseInfoRepository::class)->get();
        $baseInfo->setUcpCheckoutEnabled(true);
        $this->entityManager->flush();
    }

    // --- ライフサイクル応答 --------------------------------------------------

    public function testCreateWithoutAddressResponseMatchesCheckoutSchema(): void
    {
        $productClassId = $this->createPurchasableProductClassId();

        $raw = $this->requestJson('POST', '/ucp/checkout-sessions', $this->createPayload($productClassId, withAddress: false));
        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode(), $raw);
        $this->assertSame('incomplete', json_decode($raw, true)['status'] ?? null, '住所未確定の create は incomplete を返す前提');

        $this->assertConformsExceptKnownGaps($raw, 'A create response (status=incomplete, provisional totals) MUST satisfy shopping/checkout.json.');
    }

    public function testUpdateToReadyForCompleteResponseMatchesCheckoutSchema(): void
    {
        $productClassId = $this->createPurchasableProductClassId();
        $sessionId = $this->createSession($productClassId, withAddress: false);

        $raw = $this->requestJson('PUT', '/ucp/checkout-sessions/'.$sessionId, $this->createPayload($productClassId));
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $raw);
        $this->assertSame('ready_for_complete', json_decode($raw, true)['status'] ?? null, '住所確定後の update は ready_for_complete を返す前提');

        $this->assertConformsExceptKnownGaps($raw, 'An update response (status=ready_for_complete, Order-derived totals) MUST satisfy shopping/checkout.json.');
    }

    public function testGetResponseMatchesCheckoutSchema(): void
    {
        $sessionId = $this->createSession($this->createPurchasableProductClassId());

        $this->client->request(Request::METHOD_GET, '/ucp/checkout-sessions/'.$sessionId);
        $raw = (string) $this->client->getResponse()->getContent();
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $raw);

        $this->assertConformsExceptKnownGaps($raw, 'A get response MUST satisfy shopping/checkout.json.');
    }

    public function testBusinessErrorResponseMatchesCheckoutSchema(): void
    {
        $productClassId = $this->createPurchasableProductClassId(stock: '1');

        $payload = $this->createPayload($productClassId);
        $payload['line_items'][0]['quantity'] = 999;
        $raw = $this->requestJson('POST', '/ucp/checkout-sessions', $payload);
        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode(), 'ビジネスエラーは HTTP 4xx ではなく HTTP 2xx + messages[]');
        $this->assertNotEmpty(json_decode($raw, true)['messages'] ?? [], '在庫超過は messages[] で通知する前提');

        $this->assertConformsExceptKnownGaps($raw, 'A business-error response (HTTP 2xx + messages[]) MUST satisfy shopping/checkout.json including types/message.json.');
    }

    public function testCompleteResponseMatchesCheckoutSchema(): void
    {
        $sessionId = $this->createSession($this->createPurchasableProductClassId());

        $raw = $this->requestJson('POST', '/ucp/checkout-sessions/'.$sessionId.'/complete', []);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $raw);
        $this->assertSame('completed', json_decode($raw, true)['status'] ?? null, 'complete は completed を返す前提');

        $this->assertConformsExceptKnownGaps($raw, 'A complete response (status=completed, order confirmation) MUST satisfy shopping/checkout.json including types/order_confirmation.json.');
    }

    public function testCancelResponseMatchesCheckoutSchema(): void
    {
        $sessionId = $this->createSession($this->createPurchasableProductClassId());

        $raw = $this->requestJson('POST', '/ucp/checkout-sessions/'.$sessionId.'/cancel', []);
        $this->assertSame(Response::HTTP_OK, $this->client->getResponse()->getStatusCode(), $raw);
        $this->assertSame('canceled', json_decode($raw, true)['status'] ?? null, 'cancel は canceled を返す前提');

        $this->assertConformsExceptKnownGaps($raw, 'A cancel response (status=canceled) MUST satisfy shopping/checkout.json.');
    }

    // --- 負例: バリデータが構造制約を評価していることの証明 --------------------

    public function testStatusOutsideEnumIsRejected(): void
    {
        $document = $this->conformingDocument($this->createSession($this->createPurchasableProductClassId()));
        $document->status = 'ready';

        $this->assertInvalidUcpJson(
            self::CHECKOUT_SCHEMA_REF,
            (string) json_encode($document),
            'checkout.status MUST be one of incomplete / requires_escalation / ready_for_complete / complete_in_progress / completed / canceled ("ready" is the EC-CUBE master name, not the wire value).'
        );
    }

    public function testMissingTotalsIsRejected(): void
    {
        $document = $this->conformingDocument($this->createSession($this->createPurchasableProductClassId()));
        unset($document->totals);

        $this->assertInvalidUcpJson(
            self::CHECKOUT_SCHEMA_REF,
            (string) json_encode($document),
            'checkout.totals is REQUIRED.'
        );
    }

    // --- 既知の未充足要件 (修正 landing 後に補填を撤去して有効化する) ---------------

    /**
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/v2026-04-08/source/schemas/ucp.json#L219
     */
    #[DoesNotPerformAssertions]
    public function testCheckoutResponseUcpDeclaresPaymentHandlers(): void
    {
        self::markTestIncomplete(
            'MUST: ucp.payment_handlers is REQUIRED in checkout responses (ucp.json#/$defs/response_checkout_schema). '
            .'UcpCheckoutSessionMapper::baseResponse emits only ucp.version / ucp.status. '
            .'Enable after the mapper advertises the handlers available for the session, and drop the shim in assertConformsExceptKnownGaps().'
        );
    }

    /**
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/v2026-04-08/source/schemas/shopping/types/message_error.json#L6
     * @see https://github.com/Universal-Commerce-Protocol/ucp/blob/v2026-04-08/source/schemas/shopping/types/message_warning.json#L6
     */
    #[DoesNotPerformAssertions]
    public function testErrorAndWarningMessagesCarryCode(): void
    {
        self::markTestIncomplete(
            'MUST: message_error and message_warning REQUIRE "code" (freeform codes permitted; standard codes: out_of_stock, address_undeliverable, payment_failed, ...). '
            .'AgentCheckoutMessage has no code and UcpMessageMapper emits type / content / content_type / severity only (AcpMessageMapper already emits a code). '
            .'Enable after UcpMessageMapper emits a code, and drop the shim in assertConformsExceptKnownGaps().'
        );
    }

    // --- helpers -----------------------------------------------------------

    /**
     * 既知の未充足要件を補填したうえで、配信された生 JSON 全体を checkout.json で検証する.
     *
     * 補填の前に「まだ未充足であること」を断言するため、修正が landing すると本メソッドは失敗する.
     * そのときは補填を外し、対応する markTestIncomplete を実際の断言へ置き換えること.
     */
    private function assertConformsExceptKnownGaps(string $raw, string $message): void
    {
        $document = $this->decodeObject($raw);

        // 既知ギャップ 1: ucp.payment_handlers (testCheckoutResponseUcpDeclaresPaymentHandlers)
        $this->assertObjectHasProperty('ucp', $document);
        $this->assertObjectNotHasProperty('payment_handlers', $document->ucp, 'ucp.payment_handlers is now emitted: drop this shim and enable testCheckoutResponseUcpDeclaresPaymentHandlers.');
        $document->ucp->payment_handlers = new \stdClass();

        // 既知ギャップ 2: messages[].code (testErrorAndWarningMessagesCarryCode)
        foreach ($document->messages ?? [] as $i => $entry) {
            if (!in_array($entry->type ?? null, ['error', 'warning'], true)) {
                continue;
            }
            $this->assertObjectNotHasProperty('code', $entry, sprintf('messages[%d].code is now emitted: drop this shim and enable testErrorAndWarningMessagesCarryCode.', $i));
            $entry->code = self::PLACEHOLDER_MESSAGE_CODE;
        }

        $this->assertValidUcpJson(self::CHECKOUT_SCHEMA_REF, (string) json_encode($document), $message);
    }

    /**
     * 負例のベースとして、既知ギャップを補填済みの (= schema に適合する) get 応答を返す.
     */
    private function conformingDocument(string $sessionId): \stdClass
    {
        $this->client->request(Request::METHOD_GET, '/ucp/checkout-sessions/'.$sessionId);
        $document = $this->decodeObject((string) $this->client->getResponse()->getContent());
        $document->ucp->payment_handlers ??= new \stdClass();
        foreach ($document->messages ?? [] as $entry) {
            $entry->code ??= self::PLACEHOLDER_MESSAGE_CODE;
        }

        return $document;
    }

    private function decodeObject(string $raw): \stdClass
    {
        $document = json_decode($raw);
        $this->assertInstanceOf(\stdClass::class, $document, 'The response body MUST be a JSON object: '.$raw);

        return $document;
    }

    private function createPurchasableProductClassId(string $stock = '100'): int
    {
        $Product = $this->createProduct('UCP スキーマ契約テスト商品', 1);
        /** @var ProductClass $ProductClass */
        $ProductClass = $Product->getProductClasses()[0];
        $ProductClass->setStock($stock);
        $ProductClass->setStockUnlimited(false);
        $this->entityManager->flush();

        return (int) $ProductClass->getId();
    }

    private function createSession(int $productClassId, bool $withAddress = true): string
    {
        $raw = $this->requestJson('POST', '/ucp/checkout-sessions', $this->createPayload($productClassId, $withAddress));
        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode(), $raw);
        $sessionId = json_decode($raw, true)['id'] ?? null;
        $this->assertIsString($sessionId);

        return $sessionId;
    }

    /**
     * @param array<string, mixed> $body
     */
    private function requestJson(string $method, string $uri, array $body): string
    {
        $this->client->request($method, $uri, [], [], ['CONTENT_TYPE' => 'application/json'], (string) json_encode($body));

        return (string) $this->client->getResponse()->getContent();
    }

    /**
     * @return array<string, mixed>
     */
    private function createPayload(int $productClassId, bool $withAddress = true): array
    {
        $payload = [
            'currency' => 'JPY',
            'line_items' => [['item' => ['id' => (string) $productClassId], 'quantity' => 2]],
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
}
