<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Service\PurchaseFlow\Processor;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\ItemHolderInterface;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Order;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Service\PurchaseFlow\ItemHolderPreprocessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\TaxRuleService;

class TaxProcessor implements ItemHolderPreprocessor
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var TaxRuleRepository
     */
    protected $taxRuleRepository;

    /**
     * @var TaxRuleService
     */
    protected $taxRuleService;

    /**
     * TaxProcessor constructor.
     *
     * @param TaxRuleRepository $taxRuleRepository
     * @param TaxRuleService $taxRuleService
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TaxRuleRepository $taxRuleRepository,
        TaxRuleService $taxRuleService
    ) {
        $this->entityManager = $entityManager;
        $this->taxRuleRepository = $taxRuleRepository;
        $this->taxRuleService = $taxRuleService;
    }

    /**
     * @param ItemHolderInterface $itemHolder
     * @param PurchaseContext $context
     *
     * @throws \Doctrine\ORM\NoResultException
     */
    public function process(ItemHolderInterface $itemHolder, PurchaseContext $context)
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
                $item->setTaxDisplayType($this->getTaxDisplayType($OrderItemType));
            }

            // 税区分: 非課税, 不課税
            if ($item->getTaxType()->getId() != TaxType::TAXATION) {
                $item->setTax(0);
                $item->setTaxRate(0);
                $item->setRoundingType(null);
                $item->setTaxRuleId(null);

                return;
            }

            if ($item->getTaxRuleId()) {
                $TaxRule = $this->taxRuleRepository->find($item->getTaxRuleId());
            } else {
                $TaxRule = $this->taxRuleRepository->getByRule($item->getProduct(), $item->getProductClass());
            }

            // $TaxRuleを取得出来ない場合は基本税率設定を使用.
            if (null === $TaxRule) {
                $TaxRule = $this->taxRuleRepository->getByRule();
            }

            // 税込表示の場合は, priceが税込金額のため割り戻す.
            if ($item->getTaxDisplayType()->getId() == TaxDisplayType::INCLUDED) {
                $tax = $this->taxRuleService->calcTaxIncluded(
                    $item->getPrice(), $TaxRule->getTaxRate(), $TaxRule->getRoundingType()->getId(),
                    $TaxRule->getTaxAdjust());
            } else {
                $tax = $this->taxRuleService->calcTax(
                    $item->getPrice(), $TaxRule->getTaxRate(), $TaxRule->getRoundingType()->getId(),
                    $TaxRule->getTaxAdjust());
            }

            $item->setTax($tax);
            $item->setTaxRate($TaxRule->getTaxRate());
            $item->setRoundingType($TaxRule->getRoundingType());
            $item->setTaxRuleId($TaxRule->getId());
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
     * @param $OrderItemType
     *
     * @return TaxType
     */
    protected function getTaxType($OrderItemType)
    {
        if ($OrderItemType instanceof OrderItemType) {
            $OrderItemType = $OrderItemType->getId();
        }

        $TaxType = $OrderItemType == OrderItemType::POINT
            ? TaxType::NON_TAXABLE
            : TaxType::TAXATION;

        return $this->entityManager->find(TaxType::class, $TaxType);
    }

    /**
     * 税表示区分を取得する.
     *
     * - 商品: 税抜
     * - 送料: 税込
     * - 値引き: 税抜
     * - 手数料: 税込
     * - ポイント値引き: 税込
     *
     * @param $OrderItemType
     *
     * @return TaxType
     */
    protected function getTaxDisplayType($OrderItemType)
    {
        if ($OrderItemType instanceof OrderItemType) {
            $OrderItemType = $OrderItemType->getId();
        }

        switch ($OrderItemType) {
            case OrderItemType::PRODUCT:
                return $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::EXCLUDED);
            case OrderItemType::DELIVERY_FEE:
                return $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
            case OrderItemType::DISCOUNT:
                return $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::EXCLUDED);
            case OrderItemType::CHARGE:
                return $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
            case OrderItemType::POINT:
                return $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
            default:
                return $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::EXCLUDED);
        }
    }
}
