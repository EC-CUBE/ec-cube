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

namespace Page\Admin;

class CouponEditPage extends AbstractAdminPageStyleGuide
{
    public static function go(\AcceptanceTester $I)
    {
        $page = new self($I);
        $page->goPage('/plugin/coupon', 'クーポン管理');
        return $page;
    }

    public static function at(\AcceptanceTester $I)
    {
        $page = new CouponEditPage($I);
        $page->atPage('クーポン管理');
        return $page;
    }

    public function 入力_クーポン名($value)
    {
        $this->tester->fillField(['id' => 'coupon_coupon_name'], $value);
        return $this;
    }

    public function 選択_対象_商品()
    {
        $this->tester->click(['id' => 'coupon_coupon_type_0']);
        return $this;
    }

    public function 選択_対象_カテゴリ()
    {
        $this->tester->click(['id' => 'coupon_coupon_type_1']);
        return $this;
    }

    public function 選択_対象_全商品()
    {
        $this->tester->click(['id' => 'coupon_coupon_type_2']);
        return $this;
    }

    public function 入力_値引き額($value)
    {
        $this->tester->fillField(['id' => 'coupon_discount_price'], $value);
        return $this;
    }

    public function 入力_発行枚数($value)
    {
        $this->tester->fillField(['id' => 'coupon_coupon_release'], $value);
        return $this;
    }

    public function 入力_有効期限開始($value)
    {
        $this->tester->executeJS('$("#coupon_available_from_date").val("'.$value.'")');
        return $this;
    }

    public function 入力_有効期限終了($value)
    {
        $this->tester->executeJS('$("#coupon_available_to_date").val("'.$value.'")');
        return $this;
    }

    public function 商品追加()
    {
        $this->tester->waitForElementVisible(['id' => 'showSearchProductModal']);
        $this->tester->click(['id' => 'showSearchProductModal']);
        $this->tester->waitForElementVisible(['id' => 'searchProductModalButton']);
        $this->tester->click(['id' => 'searchProductModalButton']);
        $this->tester->waitForElementVisible(['css' => '#searchProductModalList > div > table > tbody > tr:nth-child(2) > td.text-right > button']);
        $this->tester->click(['css' => '#searchProductModalList > div > table > tbody > tr:nth-child(2) > td.text-right > button']);
        return $this;
    }

    public function 商品削除()
    {
        $this->tester->click(['css' => '#coupon_detail_list > div > div.col-1.icon_edit > button']);
        return $this;
    }

    public function カテゴリ追加()
    {
        $this->tester->waitForElementVisible(['id' => 'showSearchCategoryModal']);
        $this->tester->click(['id' => 'showSearchCategoryModal']);
        $this->tester->waitForElementVisible(['id' => 'searchCategoryModalButton']);
        $this->tester->click(['id' => 'searchCategoryModalButton']);
        $this->tester->waitForElementVisible(['css' => '#searchCategoryModalList > div > table > tbody > tr:nth-child(1) > td.text-right > button']);
        $this->tester->click(['css' => '#searchCategoryModalList > div > table > tbody > tr:nth-child(1) > td.text-right > button']);
        return $this;
    }
    public function 登録する()
    {
        $this->tester->waitForElementVisible(['css' => '#form1 > div > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button']);
        $this->tester->click(['css' => '#form1 > div > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button']);
        return $this;
    }
}
