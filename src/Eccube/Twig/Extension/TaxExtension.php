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

namespace Eccube\Twig\Extension;

use Eccube\Entity\OrderItem;
use Eccube\Repository\TaxRuleRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TaxExtension extends AbstractExtension
{
    /**
     * @var TaxRuleRepository
     */
    private $taxRuleRepository;

    /**
     * TaxExtension constructor.
     *
     * @param TaxRuleRepository $taxRuleRepository
     */
    public function __construct(TaxRuleRepository $taxRuleRepository)
    {
        $this->taxRuleRepository = $taxRuleRepository;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return TwigFunction[] An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('is_reduced_tax_rate', [$this, 'isReducedTaxRate']),
        ];
    }

    /**
     * 明細が軽減税率対象かどうかを返す.
     *
     * 受注作成時点での標準税率と比較し, 異なれば軽減税率として判定する.
     *
     * @param OrderItem $OrderItem
     *
     * @return bool
     */
    public function isReducedTaxRate(OrderItem $OrderItem)
    {
        $Order = $OrderItem->getOrder();

        $qb = $this->taxRuleRepository->createQueryBuilder('t');
        try {
            $TaxRule = $qb
                ->where('t.Product IS NULL AND t.ProductClass IS NULL AND t.apply_date < :apply_date')
                ->orderBy('t.apply_date', 'DESC')
                ->setParameter('apply_date', $Order->getCreateDate())
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (\Exception $e) {
            return false;
        }

        return $TaxRule && $TaxRule->getTaxRate() != $OrderItem->getTaxRate();
    }
}
