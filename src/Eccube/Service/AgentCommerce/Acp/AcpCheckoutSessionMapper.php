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

namespace Eccube\Service\AgentCommerce\Acp;

use Eccube\Entity\CheckoutSession;
use Eccube\Entity\Master\AgentProtocol;
use Eccube\Entity\Master\CheckoutSessionStatus;
use Eccube\Entity\Order;
use Eccube\Repository\ProductClassRepository;
use Eccube\Service\AgentCommerce\AddressMappingService;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutAddress;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutLineItem;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutRequest;
use Eccube\Service\AgentCommerce\Exception\AgentCheckoutErrorCode;
use Eccube\Service\AgentCommerce\Exception\AgentCheckoutException;
use Eccube\Service\AgentCommerce\MinorUnitConverter;
use Eccube\Service\AgentCommerce\StorefrontUrlResolver;

/**
 * ACP の REST ペイロード ↔ EC-CUBE の中立 DTO / レスポンスのマッピング.
 *
 * - リクエスト: ACP create/update の `line_items[].id` (=ProductClass id)・`quantity`、
 *   `fulfillment_details` (氏名・連絡先 + address) を {@link AgentCheckoutRequest} へ写す。
 * - レスポンス: 再計算後の `Order` (または住所未確定時の暫定見積) から ACP CheckoutSession を組み立てる。
 *   金額は {@link MinorUnitConverter} で minor unit 整数化し、`totals[]`/`line_items[].totals[]` は
 *   `{type, display_text, amount}` 形式 (display_text は ACP で必須)。代引手数料は `type:"fee"` 行に出す。
 *
 * @see https://github.com/agentic-commerce-protocol/agentic-commerce-protocol ACP openapi.agentic_checkout.yaml (CheckoutSession / LineItem / Total) 2026-04-17
 */
class AcpCheckoutSessionMapper
{
    /** ACP プロトコルバージョン (YYYY-MM-DD). */
    public const ACP_VERSION = '2026-04-17';

    /** 当実装がサポートする ACP バージョン (discovery / protocol.version と整合). */
    public const SUPPORTED_VERSIONS = ['2026-04-17'];

    public function __construct(
        private readonly MinorUnitConverter $minorUnitConverter,
        private readonly StorefrontUrlResolver $urlResolver,
        private readonly AcpStatusMapper $statusMapper,
        private readonly AddressMappingService $addressMappingService,
        private readonly ProductClassRepository $productClassRepository,
    ) {
    }

    /**
     * ACP create/update ペイロードを中立リクエスト DTO へ変換する.
     *
     * @param array<string, mixed> $payload
     */
    public function toCheckoutRequest(array $payload, ?string $agentId): AgentCheckoutRequest
    {
        $lineItems = [];
        $rawLineItems = $payload['line_items'] ?? [];
        if (is_array($rawLineItems)) {
            foreach ($rawLineItems as $rawLineItem) {
                if (!is_array($rawLineItem)) {
                    continue;
                }
                // ACP の line_items[].id はカタログの item id (= ProductClass id)。
                // id・quantity はいずれも正の整数のみ許可する (非数値→0 や負数の素通りを防ぎ、
                // 不正値は AgentCheckoutException → 400 プロトコルエラーに寄せる)。
                $itemId = filter_var($rawLineItem['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
                $quantity = filter_var($rawLineItem['quantity'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
                if ($itemId === false || $quantity === false) {
                    throw new AgentCheckoutException(AgentCheckoutErrorCode::EMPTY_LINE_ITEMS, 'Each line item must include a positive integer "id" and "quantity".');
                }

                $lineItems[] = new AgentCheckoutLineItem($itemId, $quantity);
            }
        }

        // ACP の currency は小文字 ISO 4217 (例: "usd")。EC-CUBE 内部は大文字で扱う。
        $currency = is_string($payload['currency'] ?? null) ? strtoupper($payload['currency']) : 'JPY';

        return new AgentCheckoutRequest(
            $lineItems,
            $this->extractAddress($payload),
            $currency,
            AgentProtocol::ACP,
            $agentId,
        );
    }

    /**
     * 再計算済みの `Order` から ACP CheckoutSession レスポンスを組み立てる.
     *
     * @param list<array<string, mixed>> $acpMessages
     * @param array<string, mixed>       $actionData  REQUIRES_ACTION 時の中立データ (status 振り分けに使用)
     *
     * @return array<string, mixed>
     */
    public function buildResponseFromOrder(CheckoutSession $session, Order $order, array $acpMessages, array $actionData = []): array
    {
        $currency = $session->getCurrencyCode();

        $response = $this->baseResponse($session, $acpMessages, $actionData);
        $response['line_items'] = $this->lineItemsFromOrder($order, $currency);
        $response['totals'] = $this->totalsFromOrder($order, $currency);

        if ($session->getStatus()?->getId() === CheckoutSessionStatus::COMPLETED) {
            $orderNo = (string) ($order->getOrderNo() ?? $order->getId());
            $response['order'] = [
                'id' => $orderNo,
                'checkout_session_id' => $session->getSessionId(),
                'order_number' => $orderNo,
                'permalink_url' => $this->urlResolver->orderPermalinkUrl($orderNo),
            ];
        }

        return $response;
    }

    /**
     * 住所未確定 (Order 未生成) の暫定見積レスポンスを組み立てる.
     *
     * 商品単価から subtotal を算出し、total は subtotal と同値とする (送料・税は住所確定後に再計算)。
     *
     * @param list<array<string, mixed>> $acpMessages
     *
     * @return array<string, mixed>
     */
    public function buildProvisionalResponse(CheckoutSession $session, AgentCheckoutRequest $request, array $acpMessages): array
    {
        $currency = $session->getCurrencyCode();

        $lineItems = [];
        $subtotalMinor = 0;
        foreach ($request->lineItems as $lineItem) {
            $productClass = $this->productClassRepository->find($lineItem->productClassId);
            if ($productClass === null) {
                continue;
            }
            $unitMinor = $this->minorUnitConverter->toMinorUnits((string) $productClass->getPrice02IncTax(), $currency);
            $lineTotalMinor = $unitMinor * $lineItem->quantity;
            $subtotalMinor += $lineTotalMinor;

            $lineItems[] = [
                'id' => (string) $productClass->getId(),
                'item' => ['id' => (string) $productClass->getId()],
                'name' => (string) $productClass->getProduct()?->getName(),
                'quantity' => $lineItem->quantity,
                'unit_amount' => $unitMinor,
                'totals' => [
                    $this->total('items_base_amount', 'Base Amount', $lineTotalMinor),
                    $this->total('total', 'Total', $lineTotalMinor),
                ],
            ];
        }

        $response = $this->baseResponse($session, $acpMessages, []);
        $response['line_items'] = $lineItems;
        $response['totals'] = [
            $this->total('subtotal', 'Subtotal', $subtotalMinor),
            $this->total('total', 'Total', $subtotalMinor),
        ];

        return $response;
    }

    /**
     * レスポンスの共通骨格 (id/protocol/capabilities/status/currency/messages/buyer/expires_at).
     *
     * @param list<array<string, mixed>> $acpMessages
     * @param array<string, mixed>       $actionData
     *
     * @return array<string, mixed>
     */
    private function baseResponse(CheckoutSession $session, array $acpMessages, array $actionData): array
    {
        $hasBlockingError = false;
        foreach ($acpMessages as $message) {
            if (($message['type'] ?? null) === 'error') {
                $hasBlockingError = true;
                break;
            }
        }

        $response = [
            'id' => $session->getSessionId(),
            'protocol' => ['version' => self::ACP_VERSION],
            'capabilities' => new \stdClass(),
            'status' => $this->statusMapper->toAcpStatus($session->getStatus(), $actionData, $hasBlockingError),
            'currency' => strtolower($session->getCurrencyCode()),
        ];

        if ($acpMessages !== []) {
            $response['messages'] = $acpMessages;
        }

        $buyer = $this->buyerEcho($session);
        if ($buyer !== []) {
            $response['buyer'] = $buyer;
        }

        $expiresAt = $session->getExpiresAt();
        if ($expiresAt !== null) {
            $response['expires_at'] = $expiresAt->format(\DateTimeInterface::RFC3339);
        }

        return $response;
    }

    /**
     * `Order` の明細 (商品行) を ACP line_items[] へ写す.
     *
     * @return list<array<string, mixed>>
     */
    private function lineItemsFromOrder(Order $order, string $currency): array
    {
        $lineItems = [];
        foreach ($order->getOrderItems() as $orderItem) {
            if (!$orderItem->isProduct()) {
                continue;
            }
            $productClass = $orderItem->getProductClass();
            $id = $productClass !== null ? (string) $productClass->getId() : (string) $orderItem->getId();
            $unitMinor = $this->minorUnitConverter->toMinorUnits((string) $orderItem->getPriceIncTax(), $currency);
            $totalMinor = $this->minorUnitConverter->toMinorUnits((string) $orderItem->getTotalPrice(), $currency);

            $lineItems[] = [
                'id' => $id,
                'item' => ['id' => $id],
                'name' => $orderItem->getProductName(),
                'quantity' => (int) $orderItem->getQuantity(),
                'unit_amount' => $unitMinor,
                'totals' => [
                    $this->total('items_base_amount', 'Base Amount', $totalMinor),
                    $this->total('total', 'Total', $totalMinor),
                ],
            ];
        }

        return $lineItems;
    }

    /**
     * `Order` の金額から ACP totals[] を組み立てる.
     *
     * subtotal と total は常に出力し、discount(負)/fulfillment/fee(代引手数料)/tax は非零のときのみ出力する。
     *
     * @return list<array<string, mixed>>
     */
    private function totalsFromOrder(Order $order, string $currency): array
    {
        $convert = fn (string $amount): int => $this->minorUnitConverter->toMinorUnits($amount, $currency);

        $totals = [
            $this->total('subtotal', 'Subtotal', $convert($order->getSubtotal())),
        ];

        $discount = $convert($order->getDiscount());
        if ($discount !== 0) {
            // ACP の discount は負の符号。
            $totals[] = $this->total('discount', 'Discount', -abs($discount));
        }

        $fulfillment = $convert($order->getDeliveryFeeTotal());
        if ($fulfillment !== 0) {
            $totals[] = $this->total('fulfillment', 'Fulfillment', $fulfillment);
        }

        // 代引手数料 (Payment.charge を Order に集約済み) は fee 行に出す。
        $fee = $convert($order->getCharge());
        if ($fee !== 0) {
            $totals[] = $this->total('fee', 'Fee', $fee);
        }

        $tax = $convert($order->getTax());
        if ($tax !== 0) {
            $totals[] = $this->total('tax', 'Tax', $tax);
        }

        $totals[] = $this->total('total', 'Total', $convert($order->getPaymentTotal()));

        return $totals;
    }

    /**
     * ACP Total オブジェクト ({type, display_text, amount}) を組み立てる (display_text は必須).
     *
     * @return array<string, mixed>
     */
    private function total(string $type, string $displayText, int $amount): array
    {
        return ['type' => $type, 'display_text' => $displayText, 'amount' => $amount];
    }

    /**
     * セッションの buyer_data を ACP buyer ブロックへ写す (無ければ空配列).
     *
     * @return array<string, mixed>
     */
    private function buyerEcho(CheckoutSession $session): array
    {
        $buyerData = $session->getBuyerData();
        if (!is_array($buyerData) || $buyerData === []) {
            return [];
        }

        $buyer = [];
        if (isset($buyerData['given_name'])) {
            $buyer['first_name'] = (string) $buyerData['given_name'];
        }
        if (isset($buyerData['family_name'])) {
            $buyer['last_name'] = (string) $buyerData['family_name'];
        }
        if (isset($buyerData['email'])) {
            $buyer['email'] = (string) $buyerData['email'];
        }
        if (isset($buyerData['phone'])) {
            $buyer['phone_number'] = (string) $buyerData['phone'];
        }

        return $buyer;
    }

    /**
     * ペイロードから配送先住所 (fulfillment_details / buyer) を抽出する.
     *
     * `fulfillment_details.address` (line_one/line_two/city/state/postal_code/country) を主とし、
     * 氏名・連絡先は fulfillment_details または buyer から補う。address が無ければ住所未確定とみなす
     * (送料計算ができないため update で後追い提供できる)。
     *
     * @param array<string, mixed> $payload
     */
    private function extractAddress(array $payload): ?AgentCheckoutAddress
    {
        $fulfillment = is_array($payload['fulfillment_details'] ?? null) ? $payload['fulfillment_details'] : [];
        $buyer = is_array($payload['buyer'] ?? null) ? $payload['buyer'] : [];
        $address = is_array($fulfillment['address'] ?? null) ? $fulfillment['address'] : [];

        if ($address === []) {
            return null;
        }

        [$familyName, $givenName] = $this->resolveNames($fulfillment, $buyer, $address);

        $pref = $this->addressMappingService->getPrefFromRegion($this->stringOrNull($address['state'] ?? null));
        $line1 = $this->stringOrNull($address['line_one'] ?? null);
        $line2 = $this->stringOrNull($address['line_two'] ?? null);

        return new AgentCheckoutAddress(
            name01: $familyName,
            name02: $givenName,
            kana01: null,
            kana02: null,
            companyName: null,
            postalCode: $this->stringOrNull($address['postal_code'] ?? null),
            prefId: $pref?->getId(),
            addr01: $this->stringOrNull($address['city'] ?? null),
            addr02: $this->joinLines($line1, $line2),
            email: $this->stringOrNull($buyer['email'] ?? $fulfillment['email'] ?? null),
            phoneNumber: $this->stringOrNull($buyer['phone_number'] ?? $fulfillment['phone_number'] ?? null),
        );
    }

    /**
     * 氏名 (family, given) を解決する.
     *
     * buyer.first_name/last_name を優先し、無ければ fulfillment_details.name / address.name を
     * 空白で分割する (Western 表記 "First Last" → given=先頭・family=残り)。
     *
     * @param array<string, mixed> $fulfillment
     * @param array<string, mixed> $buyer
     * @param array<string, mixed> $address
     *
     * @return array{0: ?string, 1: ?string} [family, given]
     */
    private function resolveNames(array $fulfillment, array $buyer, array $address): array
    {
        $family = $this->stringOrNull($buyer['last_name'] ?? null);
        $given = $this->stringOrNull($buyer['first_name'] ?? null);
        if ($family !== null || $given !== null) {
            return [$family, $given];
        }

        $fullName = $this->stringOrNull($fulfillment['name'] ?? $address['name'] ?? null);
        if ($fullName === null) {
            return [null, null];
        }

        $parts = preg_split('/\s+/', trim($fullName)) ?: [];
        if (count($parts) < 2) {
            return [$fullName, null];
        }

        $given = array_shift($parts);

        return [implode(' ', $parts), $given];
    }

    private function joinLines(?string $line1, ?string $line2): ?string
    {
        $joined = trim(implode(' ', array_filter([$line1, $line2], static fn (?string $v): bool => $v !== null && $v !== '')));

        return $joined === '' ? null : $joined;
    }

    private function stringOrNull(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }
}
