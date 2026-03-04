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

class OrderEditPage extends AbstractAdminPageStyleGuide
{
    public static $姓_エラーメッセージ = '#ordererInfo > div > div > div:nth-child(1) > div:nth-child(2) > div > div > div:nth-child(1) > span';
    public static $登録完了メッセージ = 'div.c-container > div.c-contentsArea > div.alert.alert-success.alert-dismissible.fade.show.m-3 > span';
    public static $ポイント値引き額 = '//span[contains(text(), "ポイント")]/parent::div/following-sibling::div/span';
    public static $利用ポイント = '#order_use_point';
    public static $加算ポイント = '//span[contains(text(), "加算ポイント")]/parent::div/following-sibling::div/span';

    /**
     * OrderRegisterPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);
        $page->goPage('/order/new', '受注登録受注管理');

        return $page;
    }

    public static function at($I)
    {
        $page = new self($I);
        $page->atPage('受注登録受注管理');

        return $page;
    }

    public function 入力_受注ステータス($value)
    {
        $this->tester->selectOption(['id' => 'order_OrderStatus'], $value);

        return $this;
    }

    public function 入力_姓($value)
    {
        $this->tester->fillField(['id' => 'order_name_name01'], $value);

        return $this;
    }

    public function 入力_名($value)
    {
        $this->tester->fillField(['id' => 'order_name_name02'], $value);

        return $this;
    }

    public function 入力_セイ($value)
    {
        $this->tester->fillField(['id' => 'order_kana_kana01'], $value);

        return $this;
    }

    public function 入力_メイ($value)
    {
        $this->tester->fillField(['id' => 'order_kana_kana02'], $value);

        return $this;
    }

    public function 入力_郵便番号($value)
    {
        $this->tester->fillField(['id' => 'order_postal_code'], $value);

        return $this;
    }

    public function 入力_都道府県($value)
    {
        $this->tester->selectOption(['id' => 'order_address_pref'], $value);

        return $this;
    }

    public function 入力_市区町村名($value)
    {
        $this->tester->fillField(['id' => 'order_address_addr01'], $value);

        return $this;
    }

    public function 入力_番地_ビル名($value)
    {
        $this->tester->fillField(['id' => 'order_address_addr02'], $value);

        return $this;
    }

    public function 入力_電話番号($value)
    {
        $this->tester->fillField(['id' => 'order_phone_number'], $value);

        return $this;
    }

    public function 入力_Eメール($value)
    {
        $this->tester->fillField(['id' => 'order_email'], $value);

        return $this;
    }

    public function 入力_支払方法($value)
    {
        $this->tester->selectOption(['id' => 'order_Payment'], $value);

        return $this;
    }

    public function 入力_配送業者($value)
    {
        $this->tester->selectOption(['id' => 'order_Shipping_Delivery'], $value);

        return $this;
    }

    public function 注文者情報をコピー()
    {
        $this->tester->click('#shippingInfo > div > div.row.mb-3 > div:nth-child(1) > button.btn.btn-ec-regular.copy-customer');

        return $this;
    }

    public function 注文者パネルを開く()
    {
        $this->tester->click('#form1 > div.c-contentsArea__cols > div > div.c-primaryCol > div:nth-child(2) > div.card-header > div > div.col-1.text-right > a');
        $this->tester->wait(1);

        return $this;
    }

    public function 商品検索($value = '')
    {
        $this->tester->scrollTo(['css' => '#orderItem > div > div.row.justify-content-between.mb-2 > div.col-6 > a.btn.btn-ec-regular.mr-2.add'], 0, -50);
        $this->tester->click(['css' => '#orderItem > div > div.row.justify-content-between.mb-2 > div.col-6 > a.btn.btn-ec-regular.mr-2.add']);
        $this->tester->waitForElementVisible(['id' => 'addProduct']);
        $this->tester->fillField(['id' => 'admin_search_product_id'], $value);
        $this->tester->click('#searchProductModalButton');
        $this->tester->waitForElementVisible('#searchProductModalList table');

        return $this;
    }

    public function 商品検索結果_選択($rowNum)
    {
        $rowNum = $rowNum * 2;
        $this->tester->click("#searchProductModalList > table > tbody > tr:nth-child(${rowNum}) > td.text-right > button");

        return $this;
    }

    public function 受注情報登録()
    {
        $this->tester->click('#form1 > div.c-conversionArea > div > div > div:nth-child(2) > div > div > button');
        $this->tester->wait(5);

        return $this;
    }

    public function 明細の項目名を取得($row)
    {
        return $this->tester->grabTextFrom("#table-form-field > tbody > tr:nth-child({$row}) > td.align-middle.w-25.pl-3");
    }

    public function 明細を削除($row)
    {
        $this->tester->scrollTo(['css' => '#order-product']);
        $this->tester->click("#table-form-field > tbody > tr:nth-child({$row}) > td.align-middle.text-right.pr-3 > div > div > div.d-inline-block.mr-3 > a");
        $this->tester->waitForElementVisible("#table-form-field > tbody > tr:nth-child({$row}) > td.align-middle.text-right.pr-3 > div > div > div.modal");

        return $this;
    }

    public function acceptDeleteModal($row)
    {
        $this->tester->click("#table-form-field > tbody > tr:nth-child({$row}) > td.align-middle.text-right.pr-3 div.modal a.delete");

        return $this;
    }

    public function お届け先の追加()
    {
        $this->tester->scrollTo(['css' => '#form1'], 0, 200);
        $this->tester->click('#shipping-add');
        $this->tester->waitForElementVisible('#confirmFormChangeModal');
        $this->tester->click(['css' => '#confirmFormChangeModal > div > div > div.modal-footer > a.btn.btn-ec-conversion']);

        return $this;
    }
}
