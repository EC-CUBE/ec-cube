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

namespace Eccube\Service\AgentCommerce\Fulfillment;

use Eccube\Entity\Delivery;
use Eccube\Entity\Master\Pref;
use Eccube\Entity\Master\SaleType;
use Eccube\Entity\ProductClass;
use Eccube\Repository\DeliveryFeeRepository;
use Eccube\Repository\DeliveryRepository;
use Eccube\Service\AgentCommerce\MinorUnitConverter;

/**
 * EC-CUBE 標準のマスタ (Delivery / DeliveryFee / Payment / DeliveryDuration) から
 * 配送方法の選択肢を組み立てる標準実装.
 *
 * - 利用可能な `Delivery` は明細の `SaleType` から `DeliveryRepository::getDeliveries()` で絞る。
 * - 送料は配送先 `Pref` と `Delivery` の `DeliveryFee` から解決 (該当行が無ければ配送不可として除外)。
 * - 代引等の手数料は `Delivery` の `PaymentOption` → `Payment::getCharge()` から解決。
 * - 配送日数は明細横断の `DeliveryDuration::getDuration()` の最大値 (負数=お取り寄せが 1 件でもあれば未確定として null)。
 *
 * 複数 `SaleType` をまたぐ注文 (複数配送分割) の厳密な表現は本標準実装の対象外であり、
 * `app/Customize` での差し替えに委ねる (フラットな選択肢一覧として返す)。
 */
class StandardFulfillmentOptionMapper implements FulfillmentOptionMapperInterface
{
    public function __construct(
        private readonly DeliveryRepository $deliveryRepository,
        private readonly DeliveryFeeRepository $deliveryFeeRepository,
        private readonly MinorUnitConverter $minorUnitConverter,
    ) {
    }

    public function mapForDestination(iterable $productClasses, Pref $pref, string $currencyCode = 'JPY'): array
    {
        $items = is_array($productClasses) ? $productClasses : iterator_to_array($productClasses);

        $saleTypes = $this->extractSaleTypes($items);
        if ($saleTypes === []) {
            return [];
        }

        $estimatedDeliveryDays = $this->resolveDeliveryDays($items);

        $options = [];
        foreach ($this->deliveryRepository->getDeliveries($saleTypes) as $Delivery) {
            $DeliveryFee = $this->deliveryFeeRepository->findOneBy([
                'Delivery' => $Delivery,
                'Pref' => $pref,
            ]);
            if ($DeliveryFee === null) {
                // 配送先に対する送料設定が無い = この配送方法では届けられない。
                continue;
            }

            $options[] = new FulfillmentOption(
                deliveryId: (int) $Delivery->getId(),
                name: (string) $Delivery->getName(),
                shippingFeeMinor: $this->minorUnitConverter->toMinorUnits($DeliveryFee->getFee() ?? '0', $currencyCode),
                currencyCode: $currencyCode,
                estimatedDeliveryDays: $estimatedDeliveryDays,
                paymentOptions: $this->resolvePaymentOptions($Delivery, $currencyCode),
            );
        }

        return $options;
    }

    /**
     * 明細から重複を除いた SaleType の一覧を取得する.
     *
     * @param array<int, ProductClass> $items
     *
     * @return array<int, SaleType>
     */
    private function extractSaleTypes(array $items): array
    {
        $saleTypes = [];
        foreach ($items as $ProductClass) {
            $SaleType = $ProductClass->getSaleType();
            if ($SaleType === null) {
                continue;
            }
            $saleTypes[(int) $SaleType->getId()] = $SaleType;
        }

        return array_values($saleTypes);
    }

    /**
     * 明細横断の配送日数 (最大値) を返す.
     *
     * お取り寄せ (duration < 0) が 1 件でもあれば未確定として null を返す。
     * 配送日数の設定が皆無の場合も null。
     *
     * @param array<int, ProductClass> $items
     */
    private function resolveDeliveryDays(array $items): ?int
    {
        $maxDays = null;
        foreach ($items as $ProductClass) {
            $DeliveryDuration = $ProductClass->getDeliveryDuration();
            if ($DeliveryDuration === null) {
                continue;
            }
            $duration = $DeliveryDuration->getDuration();
            if ($duration < 0) {
                return null;
            }
            if ($maxDays === null || $duration > $maxDays) {
                $maxDays = $duration;
            }
        }

        return $maxDays;
    }

    /**
     * 配送方法に紐づく (表示可能な) 支払方法の選択肢を返す.
     *
     * @return array<int, FulfillmentPaymentOption>
     */
    private function resolvePaymentOptions(Delivery $Delivery, string $currencyCode): array
    {
        $paymentOptions = [];
        foreach ($Delivery->getPaymentOptions() as $PaymentOption) {
            $Payment = $PaymentOption->getPayment();
            if ($Payment === null || !$Payment->isVisible()) {
                continue;
            }
            $paymentOptions[] = new FulfillmentPaymentOption(
                paymentId: (int) $Payment->getId(),
                method: (string) $Payment->getMethod(),
                chargeMinor: $this->minorUnitConverter->toMinorUnits($Payment->getCharge() ?? '0', $currencyCode),
                currencyCode: $currencyCode,
            );
        }

        return $paymentOptions;
    }
}
