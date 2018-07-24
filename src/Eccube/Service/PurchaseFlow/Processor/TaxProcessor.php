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

use Eccube\Entity\ItemInterface;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\OrderItem;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Service\PurchaseFlow\ItemPreprocessor;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\TaxRuleService;

class TaxProcessor implements ItemPreprocessor
{
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
    public function __construct(TaxRuleRepository $taxRuleRepository, TaxRuleService $taxRuleService)
    {
        $this->taxRuleRepository = $taxRuleRepository;
        $this->taxRuleService = $taxRuleService;
    }

    /**
     * @param ItemInterface $item
     * @param PurchaseContext $context
     */
    public function process(ItemInterface $item, PurchaseContext $context)
    {
        if (!$item instanceof OrderItem) {
            return;
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

        // 税込表示の場合は, priceが税込金額のため割り戻す.
        if ($item->getTaxDisplayType()->getId() == TaxDisplayType::INCLUDED) {
            $tax = $this->taxRuleService->calcTaxIncluded(
                $item->getPrice(), $TaxRule->getTaxRate(), $TaxRule->getRoundingType()->getId(), $TaxRule->getTaxAdjust());
        } else {
            $tax = $this->taxRuleService->calcTax(
                $item->getPrice(), $TaxRule->getTaxRate(), $TaxRule->getRoundingType()->getId(), $TaxRule->getTaxAdjust());
        }

        $item->setTax($tax);
        $item->setTaxRate($TaxRule->getTaxRate());
        $item->setRoundingType($TaxRule->getRoundingType());
        $item->setTaxRuleId($TaxRule->getId());
    }
}
