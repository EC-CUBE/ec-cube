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

namespace Eccube\Service;

use Eccube\Repository\TaxRuleRepository;

class TaxRuleService
{
    /**
     * @var TaxRuleRepository
     */
    protected $taxRuleRepository;

    public function __construct(TaxRuleRepository $taxRuleRepository)
    {
        $this->taxRuleRepository = $taxRuleRepository;
    }

    /**
     * 設定情報に基づいて税金の金額を返す
     *
     * @param  int                                    $price        計算対象の金額
     * @param  int|null|\Eccube\Entity\Product        $product      商品
     * @param  int|null|\Eccube\Entity\ProductClass   $productClass 商品規格
     * @param  int|null|\Eccube\Entity\Master\Pref    $pref         都道府県
     * @param  int|null|\Eccube\Entity\Master\Country $country      国
     *
     * @return double                                 税金付与した金額
     */
    public function getTax($price, $product = null, $productClass = null, $pref = null, $country = null)
    {
        /* @var $TaxRule \Eccube\Entity\TaxRule */
        $TaxRule = $this->taxRuleRepository->getByRule($product, $productClass, $pref, $country);

        return $this->calcTax($price, $TaxRule->getTaxRate(), $TaxRule->getRoundingType()->getId(), $TaxRule->getTaxAdjust());
    }

    /**
     * calcIncTax
     *
     * @param  int                                    $price        計算対象の金額
     * @param  int|null|\Eccube\Entity\Product        $product      商品
     * @param  int|null|\Eccube\Entity\ProductClass   $productClass 商品規格
     * @param  int|null|\Eccube\Entity\Master\Pref    $pref         都道府県
     * @param  int|null|\Eccube\Entity\Master\Country $country      国
     *
     * @return double
     */
    public function getPriceIncTax($price, $product = null, $productClass = null, $pref = null, $country = null)
    {
        return $price + $this->getTax($price, $product, $productClass, $pref, $country);
    }

    /**
     * 税金額を計算する
     *
     * @param  int    $price     計算対象の金額
     * @param  int    $taxRate   税率(%単位)
     * @param  int    $RoundingType  端数処理
     * @param  int    $taxAdjust 調整額
     *
     * @return double 税金額
     */
    public function calcTax($price, $taxRate, $RoundingType, $taxAdjust = 0)
    {
        $tax = $price * $taxRate / 100;
        $roundTax = $this->roundByRoundingType($tax, $RoundingType);

        return $roundTax + $taxAdjust;
    }

    /**
     * 税込金額から税金額を計算する
     *
     * @param  int    $price     計算対象の金額
     * @param  int    $taxRate   税率(%単位)
     * @param  int    $RoundingType  端数処理
     * @param  int    $taxAdjust 調整額
     *
     * @return float  税金額
     */
    public function calcTaxIncluded($price, $taxRate, $RoundingType, $taxAdjust = 0)
    {
        $tax = ($price - $taxAdjust) * $taxRate / (100 + $taxRate);

        return $this->roundByRoundingType($tax, $RoundingType);
    }

    /**
     * 課税規則に応じて端数処理を行う
     *
     * @param  integer $value    端数処理を行う数値
     * @param integer $RoundingType
     *
     * @return double        端数処理後の数値
     */
    public function roundByRoundingType($value, $RoundingType)
    {
        switch ($RoundingType) {
            // 四捨五入
            case \Eccube\Entity\Master\RoundingType::ROUND:
                $ret = round($value);
                break;
            // 切り捨て
            case \Eccube\Entity\Master\RoundingType::FLOOR:
                $ret = floor($value);
                break;
            // 切り上げ
            case \Eccube\Entity\Master\RoundingType::CEIL:
                $ret = ceil($value);
                break;
            // デフォルト:切り上げ
            default:
                $ret = ceil($value);
                break;
        }

        return $ret;
    }
}
