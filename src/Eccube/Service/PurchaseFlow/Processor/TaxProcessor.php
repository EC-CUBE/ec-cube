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

namespace Eccube\Service\PurchaseFlow\Processor;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Service\OrderHelper;
use Eccube\Service\PurchaseFlow\ItemHolderPreprocessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\TaxRuleService;

class TaxProcessor implements ItemHolderPreprocessor
{
    /**
     * TaxProcessor constructor.
     */
    public function __construct(protected EntityManagerInterface $entityManager, protected TaxRuleRepository $taxRuleRepository, protected TaxRuleService $taxRuleService, protected OrderHelper $orderHelper)
    {
    }

    /**
     * @param ItemHolderInterface $itemHolder 受注 or カート
     * @param PurchaseContext $context 購入フローのコンテキスト
     *
     * @throws NoResultException
     */
    #[\Override]
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context): void
    {
        if (!$itemHolder instanceof Order) {
            return;
        }

        foreach ($itemHolder->getOrderItems() as $item) {
            // 明細種別に応じて税区分, 税表示区分を設定する,
            $OrderItemType = $item->getOrderItemType();

            if (!$item->getTaxType()) {
                $item->setTaxType($this->getTaxType($OrderItemType));
            }
            if (!$item->getTaxDisplayType()) {
                $item->setTaxDisplayType($this->orderHelper->getTaxDisplayType($OrderItemType));
            }

            // 税区分: 非課税, 不課税
            if ($item->getTaxType() && $item->getTaxType()->getId() != TaxType::TAXATION) {
                $item->setTax('0');
                $item->setTaxRate('0');
                $item->setRoundingType();

                continue;
            }

            // 注文フロー内で税率が変更された場合を考慮し反映する
            // 受注管理画面内では既に登録された税率は自動で変更しない
            if ($context->isShoppingFlow() || $item->getRoundingType() === null) {
                $TaxRule = $item->getOrderItemType()->isProduct()
                    ? $this->taxRuleRepository->getByRule($item->getProduct(), $item->getProductClass())
                    : $this->taxRuleRepository->getByRule();

                $item->setTaxRate($TaxRule->getTaxRate())
                    ->setTaxAdjust($TaxRule->getTaxAdjust())
                    ->setRoundingType($TaxRule->getRoundingType());
            }

            // 税込表示の場合は, priceが税込金額のため割り戻す.
            if ($item->getTaxDisplayType()->getId() == TaxDisplayType::INCLUDED) {
                $tax = $this->taxRuleService->calcTaxIncluded(
                    $item->getPrice(), $item->getTaxRate(), $item->getRoundingType()->getId(),
                    $item->getTaxAdjust());
            } else {
                $tax = $this->taxRuleService->calcTax(
                    $item->getPrice(), $item->getTaxRate(), $item->getRoundingType()->getId(),
                    $item->getTaxAdjust());
            }

            $item->setTax($tax);
        }
    }

    /**
     * 税区分を取得する.
     *
     * - 商品: 課税
     * - 送料: 課税
     * - 値引き: 課税
     * - 手数料: 課税
     * - ポイント値引き: 不課税
     *
     * @param OrderItemType|int $OrderItemType 明細種別
     *
     * @return TaxType 税区分
     */
    protected function getTaxType(OrderItemType|int $OrderItemType): TaxType
    {
        if ($OrderItemType instanceof OrderItemType) {
            $OrderItemType = $OrderItemType->getId();
        }

        $TaxType = $OrderItemType == OrderItemType::POINT
            ? TaxType::NON_TAXABLE
            : TaxType::TAXATION;

        return $this->entityManager->find(TaxType::class, $TaxType);
    }
}
