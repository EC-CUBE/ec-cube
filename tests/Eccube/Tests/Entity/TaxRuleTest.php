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

namespace Eccube\Tests\Entity;

use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Entity\TaxRule;
use Eccube\Tests\EccubeTestCase;

/**
 * TaxRule test cases.
 *
 * @author Kentaro Ohkouchi
 */
class TaxRuleTest extends EccubeTestCase
{
    public function testCompareTo()
    {
        $TaxRules = [
            $this->createTaxRule('1', 3, new \DateTime()),
            $this->createTaxRule('2', 2, new \DateTime()),
            $this->createTaxRule('3', 1, new \DateTime()),
        ];
        $this->expected = $TaxRules;

        shuffle($TaxRules);
        usort($TaxRules, function ($a, $b) {
            return $a->compareTo($b);
        });
        $this->actual = $TaxRules;

        $this->verify();
    }

    public function testCompareToWithApplydate()
    {
        $TaxRules = [
            $this->createTaxRule('1', 0, new \DateTime('+2 days')),
            $this->createTaxRule('2', 2, new \DateTime('+1 days')),
            $this->createTaxRule('3', 1, new \DateTime('+1 days')),
            $this->createTaxRule('4', 3, new \DateTime()),
            $this->createTaxRule('5', 4, new \DateTime('-1 days')),
        ];
        $this->expected = $TaxRules;

        shuffle($TaxRules);
        usort($TaxRules, function ($a, $b) {
            return $a->compareTo($b);
        });
        $this->actual = $TaxRules;

        $this->verify();
    }

    public function testCompareToWithProductClass()
    {
        $TaxRules = [
            $this->createTaxRule('1', 1, new \DateTime('-1 days'), new ProductClass()),
            $this->createTaxRule('2', 1, new \DateTime('+2 days')),
            $this->createTaxRule('3', 2, new \DateTime()),
            $this->createTaxRule('4', 3, new \DateTime('-1 days')),
        ];
        $this->expected = $TaxRules;

        shuffle($TaxRules);
        usort($TaxRules, function ($a, $b) {
            return $a->compareTo($b);
        });
        $this->actual = $TaxRules;

        $this->verify();
    }

    public function testCompareToWithProducts()
    {
        $TaxRules = [
            $this->createTaxRule('1', 1, new \DateTime('-1 days'), null, new Product()),
            $this->createTaxRule('2', 1, new \DateTime('+2 days')),
            $this->createTaxRule('3', 2, new \DateTime()),
            $this->createTaxRule('4', 3, new \DateTime('-1 days')),
        ];
        $this->expected = $TaxRules;

        shuffle($TaxRules);
        usort($TaxRules, function ($a, $b) {
            return $a->compareTo($b);
        });
        $this->actual = $TaxRules;

        $this->verify();
    }

    public function testCompareToWithProductTaxRule()
    {
        $TaxRules = [
            $this->createTaxRule('1', 1, new \DateTime('-1 days'), new ProductClass(), new Product()),
            $this->createTaxRule('2', 1, new \DateTime('+2 days')),
            $this->createTaxRule('3', 2, new \DateTime()),
            $this->createTaxRule('4', 3, new \DateTime('-1 days')),
        ];
        $this->expected = $TaxRules;

        shuffle($TaxRules);
        usort($TaxRules, function ($a, $b) {
            return $a->compareTo($b);
        });
        $this->actual = $TaxRules;

        $this->verify();
    }

    /**
     * @param string $taxRate
     * @param int $sortNo
     * @param \DateTime|null $applyDate
     * @param ProductClass|null $ProductClass
     * @param Product|null $Product;
     *
     * @return TaxRule
     */
    private function createTaxRule($taxRate, $sortNo = 0, \DateTime $applyDate = null, ProductClass $ProductClass = null, Product $Product = null)
    {
        if ($applyDate === null) {
            $applyDate = new \DateTime();
        }
        $TaxRule = new TaxRule();
        $TaxRule
            ->setTaxRate($taxRate)
            ->setApplyDate($applyDate)
            ->setProductClass($ProductClass)
            ->setProduct($Product)
            ->setSortNo($sortNo);

        return $TaxRule;
    }
}
