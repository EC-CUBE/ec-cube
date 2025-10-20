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

namespace Eccube\Service;

use Eccube\Entity\BaseInfo;
use Eccube\Entity\Master\Country;
use Eccube\Entity\Master\Pref;
use Eccube\Entity\Master\RoundingType;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\TaxRuleRepository;

class TaxRuleService
{
    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @var TaxRuleRepository
     */
    protected $taxRuleRepository;

    public function __construct(TaxRuleRepository $taxRuleRepository, BaseInfoRepository $baseInfoRepository)
    {
        $this->taxRuleRepository = $taxRuleRepository;
        $this->BaseInfo = $baseInfoRepository->get();
    }

    /**
     * 設定情報に基づいて税金の金額を返す
     *
     * @param  string                                    $price        計算対象の金額
     * @param int|Product|null $product 商品
     * @param  int|ProductClass|null   $productClass 商品規格
     * @param int|Pref|null $pref 都道府県
     * @param int|Country|null $country 国
     *
     * @return string                                 税金付与した金額
     */
    public function getTax($price, $product = null, $productClass = null, $pref = null, $country = null): string
    {
        /*
         * 商品別税率が有効で商品別税率が設定されている場合は商品別税率
         * 商品別税率が有効で商品別税率が設定されていない場合は基本税率
         * 商品別税率が無効の場合は基本税率
         */
        /* @var \Eccube\Entity\TaxRule $TaxRule */
        if ($this->BaseInfo->isOptionProductTaxRule() && $productClass) {
            if ($productClass instanceof ProductClass) {
                $TaxRule = $productClass->getTaxRule() ?: $this->taxRuleRepository->getByRule(null, null, $pref, $country);
            } else {
                $TaxRule = $this->taxRuleRepository->getByRule($product, $productClass, $pref, $country);
            }
        } else {
            $TaxRule = $this->taxRuleRepository->getByRule(null, null, $pref, $country);
        }

        return $this->calcTax($price, $TaxRule->getTaxRate(), $TaxRule->getRoundingType()->getId(), $TaxRule->getTaxAdjust());
    }

    /**
     * calcIncTax
     *
     * @param  string                                    $price        計算対象の金額
     * @param int|Product|null $product 商品
     * @param  int|ProductClass|null   $productClass 商品規格
     * @param int|Pref|null $pref 都道府県
     * @param int|Country|null $country 国
     *
     * @return string
     */
    public function getPriceIncTax($price, $product = null, $productClass = null, $pref = null, $country = null): string
    {
        return bcadd($price, $this->getTax($price, $product, $productClass, $pref, $country), 2);
    }

    /**
     * 税金額を計算する
     *
     * @param  string    $price     計算対象の金額
     * @param  string    $taxRate   税率(%単位)
     * @param  int    $RoundingType  端数処理
     * @param  string    $taxAdjust 調整額
     *
     * @return string 税金額
     */
    public function calcTax($price, $taxRate, $RoundingType, $taxAdjust = '0'): string
    {
        // tax = price * taxRate / 100
        $tax = bcdiv(bcmul($price, $taxRate, 4), '100', 4);
        $roundTax = self::roundByRoundingType($tax, $RoundingType);

        return bcadd($roundTax, $taxAdjust, 2);
    }

    /**
     * 税込金額から税金額を計算する
     *
     * @param  string    $price     計算対象の金額
     * @param  string    $taxRate   税率(%単位)
     * @param  int    $RoundingType  端数処理
     * @param  string    $taxAdjust 調整額
     *
     * @return string  税金額
     */
    public function calcTaxIncluded($price, $taxRate, $RoundingType, $taxAdjust = '0'): string
    {
        // tax = (price - taxAdjust) * taxRate / (100 + taxRate)
        $priceAfterAdjust = bcsub($price, $taxAdjust, 4);
        $divisor = bcadd('100', $taxRate, 4);
        $tax = bcdiv(bcmul($priceAfterAdjust, $taxRate, 4), $divisor, 4);

        return self::roundByRoundingType($tax, $RoundingType);
    }

    /**
     * 課税規則に応じて端数処理を行う
     *
     * @param string $value    端数処理を行う数値
     * @param int $RoundingType
     *
     * @return string        端数処理後の数値
     */
    public static function roundByRoundingType($value, $RoundingType): string
    {
        $ret = match ($RoundingType) {
            // 四捨五入
            RoundingType::ROUND => bcround($value),
            // 切り捨て
            RoundingType::FLOOR => bcfloor($value),
            // 切り上げ
            RoundingType::CEIL => bcceil($value),
            // デフォルト:切り上げ
            default => bcceil($value),
        };

        return $ret;
    }
}
