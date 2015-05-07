<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Service;

class TaxRuleService
{
    /**
     * @var \Eccube\Repository\TaxRuleRepository
     */
    private $taxRuleRepository;

    /**
     * __construct
     *
     * @param \Eccube\Repository\TaxRuleRepository $taxRuleRepository
     */
    public function __construct(\Eccube\Repository\TaxRuleRepository $taxRuleRepository)
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
     * @return double                                 税金付与した金額
     */
    public function getTax($price, $product = null, $productClass = null, $pref = null, $country = null)
    {
        /* @var $TaxRule \Eccube\Entity\TaxRule */
        $TaxRule = $this->taxRuleRepository->getByRule($product, $productClass, $pref, $country);

        return $this->calcTax($price, $TaxRule->getTaxRate(), $TaxRule->getCalcRule()->getId(), $TaxRule->getTaxAdjust());
    }

    /**
     * calcIncTax
     *
     * @param  int                                    $price        計算対象の金額
     * @param  int|null|\Eccube\Entity\Product        $product      商品
     * @param  int|null|\Eccube\Entity\ProductClass   $productClass 商品規格
     * @param  int|null|\Eccube\Entity\Master\Pref    $pref         都道府県
     * @param  int|null|\Eccube\Entity\Master\Country $country      国
     * @return int
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
     * @param  int    $calcRule  端数処理
     * @param  int    $taxAdjust 調整額
     * @return double 税金額
     */
    public function calcTax($price, $taxRate, $calcRule, $taxAdjust = 0)
    {
        $tax = $price * $taxRate / 100;
        $roundTax = $this->roundByCalcRule($tax, $calcRule);

        return $roundTax + $taxAdjust;
    }

    /**
     * 課税規則に応じて端数処理を行う
     *
     * @param  float|integer $value    端数処理を行う数値
     * @param  integer       $calcRule 課税規則
     * @return double        端数処理後の数値
     */
    public function roundByCalcRule($value, $calcRule)
    {
        switch ($calcRule) {
            // 四捨五入
            case 1:
                $ret = round($value);
                break;
            // 切り捨て
            case 2:
                $ret = floor($value);
                break;
            // 切り上げ
            case 3:
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
