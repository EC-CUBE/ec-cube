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

namespace Eccube\Service\AgentCommerce\Ucp;

use Eccube\Entity\CheckoutSession;
use Eccube\Entity\Master\AgentProtocol;
use Eccube\Entity\Order;
use Eccube\Repository\ProductClassRepository;
use Eccube\Service\AgentCommerce\AddressMappingService;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutAddress;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutLineItem;
use Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutRequest;
use Eccube\Service\AgentCommerce\MinorUnitConverter;
use Eccube\Service\AgentCommerce\StorefrontUrlResolver;

/**
 * UCP の REST ペイロード ↔ EC-CUBE の中立 DTO / レスポンスのマッピング.
 *
 * - リクエスト: UCP create/update の `line_items[].item.id`(=ProductClass id)・`quantity`、
 *   `buyer`、`fulfillment` destination の postal address を {@link AgentCheckoutRequest} へ写す。
 * - レスポンス: 再計算後の `Order` (または住所未確定時の暫定見積) から UCP checkout レスポンスを組み立てる。
 *   金額は {@link MinorUnitConverter} で minor unit 整数化し、`totals` は spec の符号制約
 *   (discount は負、subtotal/fulfillment/tax/fee は非負) に従う。代引手数料は `type:"fee"` 行に出す。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp UCP checkout.json / checkout-rest.md (v2026-04-08)
 */
class UcpCheckoutSessionMapper
{
    /** UCP プロトコルバージョン (YYYY-MM-DD). */
    public const UCP_VERSION = '2026-04-08';

    public function __construct(
        private readonly MinorUnitConverter $minorUnitConverter,
        private readonly StorefrontUrlResolver $urlResolver,
        private readonly UcpStatusMapper $statusMapper,
        private readonly AddressMappingService $addressMappingService,
        private readonly ProductClassRepository $productClassRepository,
    ) {
    }

    /**
     * UCP create/update ペイロードを中立リクエスト DTO へ変換する.
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
                $itemId = $rawLineItem['item']['id'] ?? null;
                $quantity = $rawLineItem['quantity'] ?? null;

                $lineItems[] = new AgentCheckoutLineItem(
                    is_numeric($itemId) ? (int) $itemId : 0,
                    is_numeric($quantity) ? (int) $quantity : 0,
                );
            }
        }

        $currency = is_string($payload['currency'] ?? null) ? $payload['currency'] : 'JPY';

        return new AgentCheckoutRequest(
            $lineItems,
            $this->extractAddress($payload),
            $currency,
            AgentProtocol::UCP,
            $agentId,
        );
    }

    /**
     * 再計算済みの `Order` から UCP checkout レスポンスを組み立てる.
     *
     * @param list<array<string, mixed>> $ucpMessages
     *
     * @return array<string, mixed>
     */
    public function buildResponseFromOrder(CheckoutSession $session, Order $order, array $ucpMessages, ?string $continueUrl = null): array
    {
        $currency = $session->getCurrencyCode();

        $response = $this->baseResponse($session, $ucpMessages);
        $response['line_items'] = $this->lineItemsFromOrder($order, $currency);
        $response['totals'] = $this->totalsFromOrder($order, $currency);

        if ($continueUrl !== null) {
            $response['continue_url'] = $continueUrl;
        }

        if ($session->getStatus()?->getId() === \Eccube\Entity\Master\CheckoutSessionStatus::COMPLETED) {
            $response['order'] = [
                'id' => (string) ($order->getOrderNo() ?? $order->getId()),
                'permalink_url' => $this->urlResolver->orderPermalinkUrl((string) ($order->getOrderNo() ?? $order->getId())),
            ];
        }

        return $response;
    }

    /**
     * 住所未確定 (Order 未生成) の暫定見積レスポンスを組み立てる.
     *
     * 商品単価から subtotal を算出し、total は subtotal と同値とする (送料・税は住所確定後に再計算)。
     *
     * @param list<array<string, mixed>> $ucpMessages
     *
     * @return array<string, mixed>
     */
    public function buildProvisionalResponse(CheckoutSession $session, AgentCheckoutRequest $request, array $ucpMessages): array
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
                'item' => [
                    'id' => (string) $productClass->getId(),
                    'title' => (string) $productClass->getProduct()?->getName(),
                    'price' => $unitMinor,
                ],
                'quantity' => $lineItem->quantity,
                'totals' => [
                    ['type' => 'total', 'amount' => $lineTotalMinor],
                ],
            ];
        }

        $response = $this->baseResponse($session, $ucpMessages);
        $response['line_items'] = $lineItems;
        $response['totals'] = [
            ['type' => 'subtotal', 'amount' => $subtotalMinor],
            ['type' => 'total', 'amount' => $subtotalMinor],
        ];

        return $response;
    }

    /**
     * レスポンスの共通骨格 (ucp/id/status/currency/links/messages/buyer/expires_at).
     *
     * @param list<array<string, mixed>> $ucpMessages
     *
     * @return array<string, mixed>
     */
    private function baseResponse(CheckoutSession $session, array $ucpMessages): array
    {
        $hasError = false;
        foreach ($ucpMessages as $message) {
            if (($message['type'] ?? null) === 'error') {
                $hasError = true;
                break;
            }
        }

        $response = [
            'ucp' => [
                'version' => self::UCP_VERSION,
                'status' => $hasError ? 'error' : 'success',
            ],
            'id' => $session->getSessionId(),
            'status' => $this->statusMapper->toUcpStatus($session->getStatus()),
            'currency' => $session->getCurrencyCode(),
            'links' => [
                ['type' => 'privacy_policy', 'url' => $this->urlResolver->privacyPolicyUrl()],
                ['type' => 'terms_of_service', 'url' => $this->urlResolver->termsOfServiceUrl()],
            ],
        ];

        if ($ucpMessages !== []) {
            $response['messages'] = $ucpMessages;
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
     * `Order` の明細 (商品行) を UCP line_items[] へ写す.
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

            $lineItems[] = [
                'id' => $id,
                'item' => [
                    'id' => $id,
                    'title' => $orderItem->getProductName(),
                    'price' => $this->minorUnitConverter->toMinorUnits((string) $orderItem->getPriceIncTax(), $currency),
                ],
                'quantity' => (int) $orderItem->getQuantity(),
                'totals' => [
                    ['type' => 'total', 'amount' => $this->minorUnitConverter->toMinorUnits((string) $orderItem->getTotalPrice(), $currency)],
                ],
            ];
        }

        return $lineItems;
    }

    /**
     * `Order` の金額から UCP totals[] を組み立てる.
     *
     * subtotal と total は常に出力し、discount(負)/fulfillment/fee(代引手数料)/tax は非零のときのみ出力する。
     *
     * @return list<array<string, mixed>>
     */
    private function totalsFromOrder(Order $order, string $currency): array
    {
        $convert = fn (string $amount): int => $this->minorUnitConverter->toMinorUnits($amount, $currency);

        $totals = [
            ['type' => 'subtotal', 'amount' => $convert((string) $order->getSubtotal())],
        ];

        $discount = $convert((string) $order->getDiscount());
        if ($discount !== 0) {
            // UCP の discount は負の符号。
            $totals[] = ['type' => 'discount', 'amount' => -abs($discount)];
        }

        $fulfillment = $convert((string) $order->getDeliveryFeeTotal());
        if ($fulfillment !== 0) {
            $totals[] = ['type' => 'fulfillment', 'amount' => $fulfillment];
        }

        // 代引手数料 (Payment.charge を Order に集約済み) は fee 行に出す。
        $fee = $convert((string) $order->getCharge());
        if ($fee !== 0) {
            $totals[] = ['type' => 'fee', 'amount' => $fee];
        }

        $tax = $convert((string) $order->getTax());
        if ($tax !== 0) {
            $totals[] = ['type' => 'tax', 'amount' => $tax];
        }

        $totals[] = ['type' => 'total', 'amount' => $convert((string) $order->getPaymentTotal())];

        return $totals;
    }

    /**
     * セッションの buyer_data を UCP buyer ブロックへ写す (無ければ空配列).
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
     * ペイロードから配送先住所 (buyer + fulfillment destination の postal address) を抽出する.
     *
     * buyer ブロックの氏名・連絡先と、fulfillment destination の postal address を統合する。
     * 完全な fulfillment method/group negotiation は本実装では扱わず、最初の destination の
     * postal address のみを解決する (それ以上は app/Customize の拡張余地)。
     *
     * @param array<string, mixed> $payload
     */
    private function extractAddress(array $payload): ?AgentCheckoutAddress
    {
        $buyer = is_array($payload['buyer'] ?? null) ? $payload['buyer'] : [];
        $postal = $this->firstDestinationPostalAddress($payload);

        // 配送先 (postal destination) が無ければ住所未確定とみなす (buyer 連絡先のみでは
        // 送料計算ができないため)。住所は update で後追い提供できる (incomplete 経由)。
        if ($postal === []) {
            return null;
        }

        $givenName = $this->stringOrNull($buyer['first_name'] ?? $postal['first_name'] ?? null);
        $familyName = $this->stringOrNull($buyer['last_name'] ?? $postal['last_name'] ?? null);

        $pref = $this->addressMappingService->getPrefFromRegion($this->stringOrNull($postal['address_region'] ?? null));

        return new AgentCheckoutAddress(
            name01: $familyName,
            name02: $givenName,
            kana01: null,
            kana02: null,
            companyName: null,
            postalCode: $this->stringOrNull($postal['postal_code'] ?? null),
            prefId: $pref?->getId(),
            addr01: $this->stringOrNull($postal['address_locality'] ?? null),
            addr02: $this->stringOrNull($postal['street_address'] ?? null),
            email: $this->stringOrNull($buyer['email'] ?? null),
            phoneNumber: $this->stringOrNull($buyer['phone_number'] ?? $postal['phone_number'] ?? null),
        );
    }

    /**
     * fulfillment destination の最初の postal address を取り出す.
     *
     * `fulfillment.destinations[]` および `fulfillment.methods[].destinations[]` の双方を許容する。
     *
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    private function firstDestinationPostalAddress(array $payload): array
    {
        $fulfillment = $payload['fulfillment'] ?? null;
        if (!is_array($fulfillment)) {
            return [];
        }

        $destinations = [];
        if (isset($fulfillment['destinations']) && is_array($fulfillment['destinations'])) {
            $destinations = $fulfillment['destinations'];
        } elseif (isset($fulfillment['methods'][0]['destinations']) && is_array($fulfillment['methods'][0]['destinations'])) {
            $destinations = $fulfillment['methods'][0]['destinations'];
        }

        $first = $destinations[0] ?? null;

        return is_array($first) ? $first : [];
    }

    private function stringOrNull(mixed $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }
}
