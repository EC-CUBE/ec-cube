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

use Eccube\Entity\Order;

class ShippingEditPage extends AbstractAdminPageStyleGuide
{
    public static $姓_エラーメッセージ = '#shipmentOverview_0 > div > div:nth-child(3) > div:nth-child(1) > div:nth-child(1) > div > div > div:nth-child(1) > span > span > span.form-error-message';

    public static $登録完了メッセージ = '#page_admin_shipping_edit > div > div.c-contentsArea > div.alert.alert-success.alert-dismissible.fade.show.m-3 > span';

    public static function go($I)
    {
        $page = new self($I);
        $page->goPage('/shipping/new', '出荷登録受注管理');

        return $page;
    }

    public static function at(\AcceptanceTester $I)
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

    public function 入力_姓(string $value, mixed $num = 0)
    {
        $this->tester->fillField(['id' => 'form_shippings_'.$num.'_name_name01'], $value);

        return $this;
    }

    public function 入力_名(string $value, mixed $num = 0)
    {
        $this->tester->fillField(['id' => 'form_shippings_'.$num.'_name_name02'], $value);

        return $this;
    }

    public function 入力_セイ(string $value, mixed $num = 0)
    {
        $this->tester->fillField(['id' => 'form_shippings_'.$num.'_kana_kana01'], $value);

        return $this;
    }

    public function 入力_メイ(string $value, mixed $num = 0)
    {
        $this->tester->fillField(['id' => 'form_shippings_'.$num.'_kana_kana02'], $value);

        return $this;
    }

    public function 入力_郵便番号(string $value, mixed $num = 0)
    {
        $this->tester->fillField(['id' => 'form_shippings_'.$num.'_postal_code'], $value);

        return $this;
    }

    public function 入力_都道府県($value, $num = 0)
    {
        $this->tester->selectOption(['id' => 'form_shippings_'.$num.'_address_pref'], $value);

        return $this;
    }

    public function 入力_市区町村名(string $value, mixed $num = 0)
    {
        $this->tester->fillField(['id' => 'form_shippings_'.$num.'_address_addr01'], $value);

        return $this;
    }

    public function 入力_番地_ビル名(string $value, mixed $num = 0)
    {
        $this->tester->fillField(['id' => 'form_shippings_'.$num.'_address_addr02'], $value);

        return $this;
    }

    public function 入力_電話番号(string $value, mixed $num = 0)
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
        $this->tester->waitForElementVisible(['id' => 'admin_search_product_id']);
        $this->tester->fillField(['id' => 'admin_search_product_id'], $value);
        $this->tester->click('#searchProductModalButton');
        $this->tester->waitForElementVisible('#searchProductModalList table');

        return $this;
    }

    public function 商品検索結果_選択($rowNum)
    {
        $rowNum = $rowNum * 2;
        $this->tester->click("#searchProductModalList > table > tbody > tr:nth-child({$rowNum}) > td.align-middle.pe-3.text-end > button");
        $this->tester->waitForElementNotVisible('#searchProductModalList');
        $this->tester->wait(5);

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
        $this->tester->click('#shipmentOverview_'.$num.' .confirmationModal[data-type="status"]');

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
            '#shipmentOverview_'.$num.' > div > div:nth-child(5) > div:nth-child(2) > div:nth-child(3) > div > span');
    }

    /**
     * 受注を指定して出荷編集画面を開く
     */
    public static function goByOrderId(\AcceptanceTester $I, Order $Order): self
    {
        $page = new self($I);
        $page->goPage('/shipping/'.$Order->getId().'/edit', '出荷登録受注管理');

        return $page;
    }

    /**
     * YubinBangoによる住所自動入力を無効化する
     *
     * h-adrクラスを削除することでYubinBangoの監視対象から外す
     *
     * @param int $shippingNo 出荷番号（0から始まる）
     */
    public function YubinBangoを無効化(int $shippingNo = 0): self
    {
        $this->tester->executeJS("
            var container = document.getElementById('shipmentOverview_{$shippingNo}');
            if (container) {
                container.classList.remove('h-adr');
                var hadrElements = container.querySelectorAll('.h-adr');
                hadrElements.forEach(function(el) { el.classList.remove('h-adr'); });
            }
        ");
        $this->tester->wait(0.5);

        return $this;
    }

    /**
     * 注文者情報をコピー
     *
     * @param int $shippingNo 出荷番号（0から始まる）
     */
    public function 注文者情報をコピー(int $shippingNo = 0): self
    {
        $this->tester->scrollTo('#shipmentOverview_'.$shippingNo);
        $this->tester->click('.copy-orderer[data-shipping-no="'.$shippingNo.'"]');
        $this->tester->wait(1);

        return $this;
    }

    /**
     * 他の出荷情報からコピー
     *
     * @param int $targetShippingNo コピー先の出荷番号（0から始まる）
     * @param int $sourceShippingNo コピー元の出荷番号（0から始まる）
     */
    public function 他の出荷情報からコピー(int $targetShippingNo, int $sourceShippingNo): self
    {
        $this->tester->scrollTo('#shipmentOverview_'.$targetShippingNo);
        // ドロップダウンを開く
        $this->tester->click('#shipmentOverview_'.$targetShippingNo.' .btn-group .dropdown-toggle');
        $this->tester->waitForElementVisible('#shipmentOverview_'.$targetShippingNo.' .dropdown-menu');
        // コピー元を選択
        $this->tester->click('.copy-other-shipping[data-shipping-no="'.$targetShippingNo.'"][data-source-no="'.$sourceShippingNo.'"]');
        $this->tester->wait(1);

        return $this;
    }

    /**
     * 出荷情報のフィールド値を取得
     *
     * @param int $shippingNo 出荷番号（0から始まる）
     * @param string $field フィールド名
     */
    public function 出荷情報のフィールド値を取得(int $shippingNo, string $field): string
    {
        $fieldMap = [
            'name01' => 'name_name01',
            'name02' => 'name_name02',
            'kana01' => 'kana_kana01',
            'kana02' => 'kana_kana02',
            'postal_code' => 'postal_code',
            'pref' => 'address_pref',
            'addr01' => 'address_addr01',
            'addr02' => 'address_addr02',
            'phone_number' => 'phone_number',
            'company_name' => 'company_name',
        ];

        if (!array_key_exists($field, $fieldMap)) {
            throw new \InvalidArgumentException('Unknown field: '.$field);
        }

        return $this->tester->grabValueFrom(['id' => 'form_shippings_'.$shippingNo.'_'.$fieldMap[$field]]);
    }
}
