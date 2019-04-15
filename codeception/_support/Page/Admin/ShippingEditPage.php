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

class ShippingEditPage extends AbstractAdminPageStyleGuide
{
    public static $姓_エラーメッセージ = '#shipmentOverview_0 > div > div:nth-child(2) > div:nth-child(1) > div:nth-child(1) > div > div > div:nth-child(1) > span > span > span.form-error-message';

    public static $登録完了メッセージ = '#page_admin_shipping_edit > div > div.c-contentsArea > div.alert.alert-success.alert-dismissible.fade.show.m-3 > span';

    /**
     * ShippingRegisterPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);
        $page->goPage('/shipping/new', '出荷登録受注管理');

        return $page;
    }

    /**
     * @param \AcceptanceTester $I
     */
    public static function at($I)
    {
        $page = new self($I);
        $page->atPage('出荷登録受注管理');

        return $page;
    }

    public function 入力_出荷日($value)
    {
        $this->tester->executeJS("document.getElementById('shipping_shipping_date').value = '{$value}'");

        return $this;
    }

    public function お届け先編集()
    {
        $this->tester->click(['id' => 'edit-shipping-addr']);
        $this->tester->waitForElementVisible(['id' => 'shipping_name_name01']);

        return $this;
    }

    /**
     * @param string $value
     */
    public function 入力_姓($value, $num = 0)
    {
        $this->tester->fillField(['id' => 'form_shippings_'.$num.'_name_name01'], $value);

        return $this;
    }

    /**
     * @param string $value
     */
    public function 入力_名($value, $num = 0)
    {
        $this->tester->fillField(['id' => 'form_shippings_'.$num.'_name_name02'], $value);

        return $this;
    }

    /**
     * @param string $value
     */
    public function 入力_セイ($value, $num = 0)
    {
        $this->tester->fillField(['id' => 'form_shippings_'.$num.'_kana_kana01'], $value);

        return $this;
    }

    /**
     * @param string $value
     */
    public function 入力_メイ($value, $num = 0)
    {
        $this->tester->fillField(['id' => 'form_shippings_'.$num.'_kana_kana02'], $value);

        return $this;
    }

    /**
     * @param string $value
     */
    public function 入力_郵便番号($value, $num = 0)
    {
        $this->tester->fillField(['id' => 'form_shippings_'.$num.'_postal_code'], $value);

        return $this;
    }

    public function 入力_都道府県($value, $num = 0)
    {
        $this->tester->selectOption(['id' => 'form_shippings_'.$num.'_address_pref'], $value);

        return $this;
    }

    /**
     * @param string $value
     */
    public function 入力_市区町村名($value, $num = 0)
    {
        $this->tester->fillField(['id' => 'form_shippings_'.$num.'_address_addr01'], $value);

        return $this;
    }

    /**
     * @param string $value
     */
    public function 入力_番地_ビル名($value, $num = 0)
    {
        $this->tester->fillField(['id' => 'form_shippings_'.$num.'_address_addr02'], $value);

        return $this;
    }

    /**
     * @param string $value
     */
    public function 入力_電話番号($value, $num = 0)
    {
        $this->tester->fillField(['id' => 'form_shippings_'.$num.'_phone_number'], $value);

        return $this;
    }

    public function 入力_出荷伝票番号($value, $num = 0)
    {
        $this->tester->fillField(['id' => 'form_shippings_'.$num.'_tracking_number'], $value);

        return $this;
    }

    public function 入力_配送業者($value, $num = 0)
    {
        $this->tester->selectOption(['id' => 'form_shippings_'.$num.'_Delivery'], $value);

        return $this;
    }

    public function 入力_配達用メモ($value, $num = 0)
    {
        $this->tester->fillField(['id' => 'form_shippings_'.$num.'_note'], $value);

        return $this;
    }

    public function 商品検索($value = '')
    {
        $this->tester->scrollTo(['css' => '#shipping-product_1 > div > button'], 0, -50);
        $this->tester->click(['css' => '#shipping-product_1 > div > button']);
        $this->tester->waitForElementVisible(['id' => 'addProduct']);
        $this->tester->fillField(['id' => 'admin_search_product_id'], $value);
        $this->tester->click('#searchProductModalButton');
        $this->tester->waitForElementVisible('#searchProductModalList table');

        return $this;
    }

    public function 商品検索結果_選択($rowNum)
    {
        $rowNum = $rowNum * 2;
        $this->tester->click("#searchProductModalList > table > tbody > tr:nth-child(${rowNum}) > td.align-middle.pr-3.text-right > button");

        return $this;
    }

    public function 出荷情報登録()
    {
        $this->tester->click(['id' => 'btn_save']);

        return $this;
    }

    public function 出荷完了にする($num = 0)
    {
        $this->tester->scrollTo(['id' => 'shipmentOverview_'.$num], 0, 50);
        $this->tester->click('#shipmentOverview_'.$num.' > div > div:nth-child(4) > div:nth-child(2) > div:nth-child(3) > div > button');

        return $this;
    }

    public function 変更を確定()
    {
        $this->tester->waitForElementVisible(['id' => 'bulkChange']);
        $this->tester->click(['id' => 'bulkChange']);
        $this->tester->waitForElementVisible(['id' => 'bulkChangeComplete']);
        $this->tester->click(['id' => 'bulkChangeComplete']);

        return $this;
    }

    public function 出荷先を追加()
    {
        $this->tester->scrollTo(['id' => 'addShipping'], 0, 50);
        $this->tester->click(['id' => 'addShipping']);

        return $this;
    }

    public function 出荷日を確認($num = 0)
    {
        $this->tester->scrollTo(['id' => 'shipmentOverview_'.$num], 0, 50);
        $this->tester->see((new \DateTime())->format('Y/m/d'),
                            '#shipmentOverview_'.$num.' > div > div:nth-child(4) > div:nth-child(2) > div:nth-child(3) > div > span');
    }
}
