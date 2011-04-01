<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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

// {{{ requires
require_once CLASS_EX_REALDIR . 'page_extends/admin/order/LC_Page_Admin_Order_Ex.php';

/**
 * 受注修正 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Order_Edit extends LC_Page_Admin_Order_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'order/edit.tpl';
        $this->tpl_subnavi = 'order/subnavi.tpl';
        $this->tpl_mainno = 'order';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrORDERSTATUS = $masterData->getMasterData("mtb_order_status");
        $this->arrDeviceType = $masterData->getMasterData('mtb_device_type');

        $objDate = new SC_Date_Ex(RELEASE_YEAR);
        $this->arrYearShippingDate = $objDate->getYear('', date('Y'), '');
        $this->arrMonthShippingDate = $objDate->getMonth(true);
        $this->arrDayShippingDate = $objDate->getDay(true);

        // 支払い方法の取得
        $this->arrPayment = SC_Helper_DB_Ex::sfGetIDValueList("dtb_payment", "payment_id", "payment_method");

        // 配送業者の取得
        $this->arrDeliv = SC_Helper_DB_Ex::sfGetIDValueList("dtb_deliv", "deliv_id", 'name');

        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objFormParam = new SC_FormParam_Ex();

        // パラメータ情報の初期化
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_REQUEST);
        $objFormParam->convParam();
        $order_id = $objFormParam->getValue('order_id');

        // DBから受注情報を読み込む
        if (!SC_Utils_Ex::isBlank($order_id)) {
            $this->setOrderToFormParam($objFormParam, $order_id);
            $this->tpl_subno = 'index';
            $this->tpl_subtitle = '受注管理';
        } else {
            $this->tpl_subno = 'add';
            $this->tpl_mode = 'add';
            $this->tpl_subtitle = '新規受注入力';
        }

        $this->arrSearchHidden = $objFormParam->getSearchArray();

        switch($this->getMode()) {
        case 'pre_edit':
        case 'order_id':
            break;

        case 'edit':
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();
            $this->arrErr = $this->lfCheckError($objFormParam);
            if (SC_Utils_Ex::isBlank($this->arrErr)) {
                $message = '受注を編集しました。';
                $order_id = $this->doRegister($order_id, $objPurchase, $objFormParam, $message);
                if ($order_id >= 0) {
                    $this->setOrderToFormParam($objFormParam, $order_id);
                }
                $this->tpl_onload = "window.alert('" . $message . "');";
            }
            break;

        case 'add':
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                $this->arrErr = $this->lfCheckError($objFormParam);
                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    $message = '新規受注を登録しました。';
                    $order_id = $this->doRegister(null, $objPurchase, $objFormParam, $message);
                    if ($order_id >= 0) {
                        $this->tpl_mode = 'edit';
                        $objFormParam->setValue('order_id', $order_id);
                        $this->setOrderToFormParam($objFormParam, $order_id);
                    }
                    $this->tpl_onload = "window.alert('" . $message . "');";
                }
            }

            break;

        // 再計算
        case 'recalculate':
        //支払い方法の選択
        case 'payment':
        // 配送業者の選択
        case 'deliv':
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();
            $this->arrErr = $this->lfCheckError($objFormParam);
            break;

        // 商品削除
        case 'delete_product':
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();
            $delete_no = $objFormParam->getValue('delete_no');
            $this->doDeleteProduct($delete_no, $objFormParam);
            $this->arrErr = $this->lfCheckError($objFormParam);
            break;

        // 商品追加ポップアップより商品選択
        case 'select_product_detail':
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();
            $this->doRegisterProduct($objFormParam);
            $this->arrErr = $this->lfCheckError($objFormParam);
            break;

        // 顧客検索ポップアップより顧客指定
        case 'search_customer':
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();
            $this->setCustomerTo($objFormParam->getValue('edit_customer_id'),
                                 $objFormParam);
            $this->arrErr = $this->lfCheckError($objFormParam);
            break;

        // 複数配送設定表示
        case 'multiple':
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();
            $this->arrErr = $this->lfCheckError($objFormParam);
            break;

        // 複数配送設定を反映
        case 'multiple_set_to':
            $this->lfInitMultipleParam($objFormParam);
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();
            $this->setMultipleItemTo($objFormParam);
            break;

        // お届け先の追加
        case 'append_shipping':
            $objFormParam->setParam($_POST);
            $objFormParam->convParam();
            $this->addShipping($objFormParam);
            break;

        default:
        }

        $this->arrForm = $objFormParam->getFormParamList();
        $this->arrDelivTime = $objPurchase->getDelivTime($objFormParam->getValue('deliv_id'));
        $this->tpl_onload .= $this->getAnchorKey($objFormParam);
        $this->arrInfo = SC_Helper_DB_Ex::sfGetBasisData();
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * パラメータ情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        // 検索条件のパラメータを初期化
        parent::lfInitParam($objFormParam);

        // お客様情報
        $objFormParam->addParam("顧客名1", "order_name01", STEXT_LEN, 'KVa', array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("顧客名2", "order_name02", STEXT_LEN, 'KVa', array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("顧客名カナ1", "order_kana01", STEXT_LEN, 'KVCa', array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("顧客名カナ2", "order_kana02", STEXT_LEN, 'KVCa', array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("メールアドレス", "order_email", MTEXT_LEN, 'KVCa', array("NO_SPTAB", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("郵便番号1", "order_zip01", ZIP01_LEN, 'n', array("NUM_CHECK", "NUM_COUNT_CHECK"));
        $objFormParam->addParam("郵便番号2", "order_zip02", ZIP02_LEN, 'n', array("NUM_CHECK", "NUM_COUNT_CHECK"));
        $objFormParam->addParam("都道府県", "order_pref", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("住所1", "order_addr01", MTEXT_LEN, 'KVa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("住所2", "order_addr02", MTEXT_LEN, 'KVa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("電話番号1", "order_tel01", TEL_ITEM_LEN, 'n', array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("電話番号2", "order_tel02", TEL_ITEM_LEN, 'n', array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("電話番号3", "order_tel03", TEL_ITEM_LEN, 'n', array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));

        // 受注商品情報
        $objFormParam->addParam("値引き", 'discount', INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
        $objFormParam->addParam("送料", "deliv_fee", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
        $objFormParam->addParam("手数料", 'charge', INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');

        // ポイント機能ON時のみ
        if (USE_POINT !== false) {
            $objFormParam->addParam("利用ポイント", "use_point", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        }

        $objFormParam->addParam("配送業者", "deliv_id", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("お支払い方法", "payment_id", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("対応状況", 'status', INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("お支払方法名称", "payment_method");

        // 受注詳細情報
        $objFormParam->addParam("商品項番", "product_class_id", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
        $objFormParam->addParam("商品種別ID", "product_type_id", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
        $objFormParam->addParam("単価", 'price', INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
        $objFormParam->addParam("数量", 'quantity', INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
        $objFormParam->addParam("商品ID", "product_id", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
        $objFormParam->addParam("商品規格ID", "product_class_id", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
        $objFormParam->addParam("ポイント付与率", "point_rate");
        $objFormParam->addParam("商品コード", "product_code");
        $objFormParam->addParam("商品名", "product_name");
        $objFormParam->addParam("規格名1", "classcategory_name1");
        $objFormParam->addParam("規格名2", "classcategory_name2");
        $objFormParam->addParam("メモ", 'note', MTEXT_LEN, 'KVa', array("MAX_LENGTH_CHECK"));
        $objFormParam->addParam("削除用項番", "delete_no", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        // DB読込用
        $objFormParam->addParam("小計", 'subtotal');
        $objFormParam->addParam("合計", 'total');
        $objFormParam->addParam("支払い合計", "payment_total");
        $objFormParam->addParam("加算ポイント", "add_point");
        $objFormParam->addParam("お誕生日ポイント", "birth_point");
        $objFormParam->addParam("消費税合計", 'tax');
        $objFormParam->addParam("最終保持ポイント", "total_point");
        $objFormParam->addParam("顧客ID", "customer_id", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
        $objFormParam->addParam("顧客ID", "edit_customer_id", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
        $objFormParam->addParam("現在のポイント", "customer_point");
        $objFormParam->addParam("受注前ポイント", 'point');
        $objFormParam->addParam("注文番号", "order_id");
        $objFormParam->addParam("受注日", "create_date");
        $objFormParam->addParam("発送日", "commit_date");
        $objFormParam->addParam("備考", 'message');
        $objFormParam->addParam("入金日", "payment_date");
        $objFormParam->addParam("アクセス端末", "device_type_id");

        // 複数情報
        $objFormParam->addParam("配送数", "shipping_quantity", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), 1);
        $objFormParam->addParam("配送ID", "shipping_id", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), 0);
        $objFormParam->addParam("お名前1", "shipping_name01", STEXT_LEN, 'KVa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("お名前2", "shipping_name02", STEXT_LEN, 'KVa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("お名前(フリガナ・姓)", "shipping_kana01", STEXT_LEN, 'KVCa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("お名前(フリガナ・名)", "shipping_kana02", STEXT_LEN, 'KVCa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("郵便番号1", "shipping_zip01", ZIP01_LEN, 'n', array("NUM_CHECK", "NUM_COUNT_CHECK"));
        $objFormParam->addParam("郵便番号2", "shipping_zip02", ZIP02_LEN, 'n', array("NUM_CHECK", "NUM_COUNT_CHECK"));
        $objFormParam->addParam("都道府県", "shipping_pref", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("住所1", "shipping_addr01", MTEXT_LEN, 'KVa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("住所2", "shipping_addr02", MTEXT_LEN, 'KVa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("電話番号1", "shipping_tel01", TEL_ITEM_LEN, 'n', array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("電話番号2", "shipping_tel02", TEL_ITEM_LEN, 'n', array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("電話番号3", "shipping_tel03", TEL_ITEM_LEN, 'n', array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("お届け時間ID", "time_id", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("お届け日(年)", "shipping_date_year", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("お届け日(月)", "shipping_date_month", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("お届け日(日)", "shipping_date_day", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("お届け日", "shipping_date", STEXT_LEN, 'KVa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("配送商品数", "shipping_product_quantity", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));

        $objFormParam->addParam("商品規格ID", "shipment_product_class_id", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("商品コード", "shipment_product_code");
        $objFormParam->addParam("商品名", "shipment_product_name");
        $objFormParam->addParam("規格名1", "shipment_classcategory_name1");
        $objFormParam->addParam("規格名2", "shipment_classcategory_name2");
        $objFormParam->addParam("単価", "shipment_price", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
        $objFormParam->addParam("数量", "shipment_quantity", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"), '0');

        $objFormParam->addParam("商品項番", 'no', INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("追加商品規格ID", "add_product_class_id", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("修正商品規格ID", "edit_product_class_id", INT_LEN, 'n', array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("アンカーキー", "anchor_key", STEXT_LEN, 'KVa', array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    }

    /**
     * 複数配送用フォームの初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitMultipleParam(&$objFormParam) {
        $objFormParam->addParam("商品規格ID", "multiple_product_class_id", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("商品コード", "multiple_product_code", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), 1);
        $objFormParam->addParam("商品名", "multiple_product_name", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), 1);
        $objFormParam->addParam("規格1", "multiple_classcategory_name1", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), 1);
        $objFormParam->addParam("規格2", "multiple_classcategory_name2", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), 1);
        $objFormParam->addParam("単価", "multiple_price", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), 1);
        $objFormParam->addParam("数量", "multiple_quantity", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), 1);
        $objFormParam->addParam("配送先住所", "multiple_shipping_id", INT_LEN, 'n', array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
    }

    /**
     * 複数配送入力フォームで入力された値を SC_FormParam へ設定する.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function setMultipleItemTo(&$objFormParam) {
        $arrMultipleKey = array('multiple_shipping_id',
                                'multiple_product_class_id',
                                'multiple_product_name',
                                'multiple_product_code',
                                'multiple_classcategory_name1',
                                'multiple_classcategory_name2',
                                'multiple_price',
                                'multiple_quantity');
        $arrMultipleParams = $objFormParam->getSwapArray($arrMultipleKey);

        /*
         * 複数配送フォームの入力値を shipping_id ごとにマージ
         *
         * $arrShipmentItem[配送先ID][商品規格ID]['shipment_(key)'] = 値
         */
        $arrShipmentItem = array();
        foreach ($arrMultipleParams as $arrMultiple) {
            $shipping_id = $arrMultiple['multiple_shipping_id'];
            $product_class_id = $arrMultiple['multiple_product_class_id'];
            foreach ($arrMultiple as $key => $val) {
                if ($key == 'multiple_quantity') {
                    $arrShipmentItem[$shipping_id][$product_class_id][str_replace('multiple', 'shipment', $key)] += $val;
                } else {
                    $arrShipmentItem[$shipping_id][$product_class_id][str_replace('multiple', 'shipment', $key)] = $val;
                }
            }
        }

        /*
         * フォームの配送先ごとの配列を生成
         *
         * $arrShipmentForm['(key)'][$shipping_index][$item_index] = 値
         * $arrProductQuantity[$shipping_index] = 配送先ごとの配送商品数
         */
        $arrShipmentForm = array();
        $arrProductQuantity = array();
        $arrShippingIds = $objFormParam->getValue('shipping_id');
        foreach ($arrShippingIds as $shipping_index => $shipping_id) {
            $item_index = 0;
            foreach ($arrShipmentItem[$shipping_id] as $product_class_id => $shipment_item) {
                foreach ($shipment_item as $key => $val) {
                    $arrShipmentForm[$key][$shipping_index][$item_index] = $val;
                }
                // 受注商品の数量を設定
                $arrQuantity[$product_class_id] += $shipment_item['shipment_quantity'];
                $item_index++;
            }
            // 配送先ごとの配送商品数を設定
            $arrProductQuantity[$shipping_index] = count($arrShipmentItem[$shipping_id]);
        }

        $objFormParam->setParam($arrShipmentForm);
        $objFormParam->setValue('shipping_product_quantity', $arrProductQuantity);

        // 受注商品の数量を変更
        $arrDest = array();
        foreach ($arrQuantity as $product_class_id => $quantity) {
            foreach ($objFormParam->getValue('product_class_id') as $n => $order_product_class_id) {
                if ($product_class_id == $order_product_class_id) {
                    $arrDest['quantity'][$n] = $quantity;
                }
            }
        }
        $objFormParam->setParam($arrDest);
    }

    /**
     * 受注データを取得して, SC_FormParam へ設定する.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param integer $order_id 取得元の受注ID
     * @return void
     */
    function setOrderToFormParam(&$objFormParam, $order_id) {
        $objPurchase = new SC_Helper_Purchase_Ex();

        // 受注詳細を設定
        $arrOrderDetail = $objPurchase->getOrderDetail($order_id, false);
        $objFormParam->setParam(SC_Utils_Ex::sfSwapArray($arrOrderDetail));

        $arrShippings = $objPurchase->getShippings($order_id);
        // お届け日の処理
        foreach (array_keys($arrShippings) as $key) {
            $shipping =& $arrShippings[$key];
            if (!SC_Utils_Ex::isBlank($shipping["shipping_date"])) {
                $ts = strtotime($shipping["shipping_date"]);
                $arrShippings[$key]['shipping_date_year'] = date('Y', $ts);
                $arrShippings[$key]['shipping_date_month'] = date('n', $ts);
                $arrShippings[$key]['shipping_date_day'] = date('j', $ts);
            }
        }
        $objFormParam->setValue('shipping_quantity', count($arrShippings));
        $objFormParam->setParam(SC_Utils_Ex::sfSwapArray($arrShippings));

        /*
         * 配送商品を設定
         *
         * $arrShipmentItem['shipment_(key)'][$shipping_index][$item_index] = 値
         * $arrProductQuantity[$shipping_index] = 配送先ごとの配送商品数
         */
        $arrProductQuantity = array();
        $arrShipmentItem = array();
        foreach ($arrShippings as $shipping_index => $arrShipping) {
            $arrProductQuantity[$shipping_index] = count($arrShipping['shipment_item']);
            foreach ($arrShipping['shipment_item'] as $item_index => $arrItem) {
                foreach ($arrItem as $item_key => $item_val) {
                    $arrShipmentItem['shipment_' . $item_key][$shipping_index][$item_index] = $item_val;
                }
            }
        }
        $objFormParam->setValue('shipping_product_quantity', $arrProductQuantity);
        $objFormParam->setParam($arrShipmentItem);

        /*
         * 受注情報を設定
         * $arrOrderDetail と項目が重複しており, $arrOrderDetail は連想配列の値
         * が渡ってくるため, $arrOrder で上書きする.
         */
        $arrOrder = $objPurchase->getOrder($order_id);
        $objFormParam->setParam($arrOrder);

        // XXX ポイントを設定
        list($db_point, $rollback_point) = SC_Helper_DB_Ex::sfGetRollbackPoint($order_id, $arrOrder['use_point'], $arrOrder['add_point']);
        $objFormParam->setValue('total_point', $db_point);
        $objFormParam->setValue('point', $rollback_point);

        if (!SC_Utils_Ex::isBlank($objFormParam->getValue('customer_id'))) {
            $this->setCustomerTo($objFormParam->getValue('customer_id'),
                                 $objFormParam);
        }
    }

    /**
     * 入力内容のチェックを行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return array エラーメッセージの配列
     */
    function lfCheckError(&$objFormParam) {
        $arrErr = $objFormParam->checkError();

        if (!SC_Utils_Ex::isBlank($objErr->arrErr)) {
            return $arrErr;
        }

        $arrValues = $objFormParam->getHashArray();

        // 商品の種類数
        $max = count($arrValues['quantity']);
        $subtotal = 0;
        $totalpoint = 0;
        $totaltax = 0;
        for($i = 0; $i < $max; $i++) {
            // 小計の計算
            $subtotal += SC_Helper_DB_Ex::sfCalcIncTax($arrValues['price'][$i]) * $arrValues['quantity'][$i];
            // 小計の計算
            $totaltax += SC_Helper_DB_Ex::sfTax($arrValues['price'][$i]) * $arrValues['quantity'][$i];
            // 加算ポイントの計算
            $totalpoint += SC_Utils_Ex::sfPrePoint($arrValues['price'][$i], $arrValues['point_rate'][$i]) * $arrValues['quantity'][$i];

            // 在庫数のチェック
            $objProduct = new SC_Product_Ex();
            $arrProduct = $objProduct->getDetailAndProductsClass($arrValues['product_class_id'][$i]);

            // 編集前の値と比較するため受注詳細を取得
            $objPurchase = new SC_Helper_Purchase_Ex();
            $arrOrderDetail = SC_Utils_Ex::sfSwapArray($objPurchase->getOrderDetail($objFormParam->getValue('order_id'), false));

            if ($arrProduct['stock_unlimited'] != '1'
                && $arrProduct['stock'] < $arrValues['quantity'][$i] - $arrOrderDetail['quantity'][$i]) {
                $class_name1 = $arrValues['classcategory_name1'][$i];
                $class_name1 = SC_Utils_Ex::isBlank($class_name1) ? 'なし' : $class_name1;
                $class_name2 = $arrValues['classcategory_name2'][$i];
                $class_name2 = SC_Utils_Ex::isBlank($class_name2) ? 'なし' : $class_name2;
                $arrErr['quantity'][$i] .= $arrValues['product_name'][$i]
                    . '/(' . $class_name1 . ')/(' . $class_name2 . ') の在庫が不足しています。 設定できる数量は「' 
                    . ($arrOrderDetail['quantity'][$i] + $arrProduct['stock']) . '」までです。<br />';
            }
        }

        // 消費税
        $arrValues['tax'] = $totaltax;
        // 小計
        $arrValues['subtotal'] = $subtotal;
        // 合計
        $arrValues['total'] = $subtotal - $arrValues['discount'] + $arrValues['deliv_fee'] + $arrValues['charge'];
        // お支払い合計
        $arrValues['payment_total'] = $arrValues['total'] - ($arrValues['use_point'] * POINT_VALUE);

        // 加算ポイント
        $arrValues['add_point'] = SC_Helper_DB_Ex::sfGetAddPoint($totalpoint, $arrValues['use_point']);

        // 最終保持ポイント
        $arrValues['total_point'] = $objFormParam->getValue('point') - $arrValues['use_point'] + $arrValues['add_point'];

        if ($arrValues['total'] < 0) {
            $arrErr['total'] = '合計額がマイナス表示にならないように調整して下さい。<br />';
        }

        if ($arrValues['payment_total'] < 0) {
            $arrErr['payment_total'] = 'お支払い合計額がマイナス表示にならないように調整して下さい。<br />';
        }

        if ($arrValues['total_point'] < 0) {
            $arrErr['use_point'] = '最終保持ポイントがマイナス表示にならないように調整して下さい。<br />';
        }

        $objFormParam->setParam($arrValues);
        return $arrErr;
    }

    /**
     * DB更新処理
     *
     * @param integer $order_id 受注ID
     * @param SC_Helper_Purchase $objPurchase SC_Helper_Purchase インスタンス
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param string $message 通知メッセージ
     * @return integer $order_id 受注ID
     *
     * エラー発生時は負数を返す。
     */
    function doRegister($order_id, &$objPurchase, &$objFormParam, &$message) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrValues = $objFormParam->getDbArray();

        $where = "order_id = ?";

        $objQuery->begin();

        // 受注テーブルの更新
        $order_id = $objPurchase->registerOrder($order_id, $arrValues);

        $arrDetail = $objFormParam->getSwapArray(array("product_id",
                                                       "product_class_id",
                                                       "product_code",
                                                       "product_name",
                                                       'price', 'quantity',
                                                       "point_rate",
                                                       "classcategory_name1",
                                                       "classcategory_name2"));

        // 変更しようとしている商品情報とDBに登録してある商品情報を比較することで、更新すべき数量を計算
        $max = count($arrDetail);
        $k = 0;
        $arrStockData = array();
        for($i = 0; $i < $max; $i++) {
            if (!empty($arrDetail[$i]['product_id'])) {
                $arrPreDetail = $objQuery->select('*', "dtb_order_detail", "order_id = ? AND product_class_id = ?", array($order_id, $arrDetail[$i]['product_class_id']));
                if (!empty($arrPreDetail) && $arrPreDetail[0]['quantity'] != $arrDetail[$i]['quantity']) {
                    // 数量が変更された商品
                    $arrStockData[$k]['product_class_id'] = $arrDetail[$i]['product_class_id'];
                    $arrStockData[$k]['quantity'] = $arrPreDetail[0]['quantity'] - $arrDetail[$i]['quantity'];
                    ++$k;
                } elseif (empty($arrPreDetail)) {
                    // 新しく追加された商品 もしくは 違う商品に変更された商品
                    $arrStockData[$k]['product_class_id'] = $arrDetail[$i]['product_class_id'];
                    $arrStockData[$k]['quantity'] = -$arrDetail[$i]['quantity'];
                    ++$k;
                }
                $objQuery->delete("dtb_order_detail", "order_id = ? AND product_class_id = ?", array($order_id, $arrDetail[$i]['product_class_id']));
            }
        }

        // 上記の新しい商品のループでDELETEされなかった商品は、注文より削除された商品
        $arrPreDetail = $objQuery->select('*', "dtb_order_detail", "order_id = ?", array($order_id));
        foreach ($arrPreDetail AS $key=>$val) {
            $arrStockData[$k]['product_class_id'] = $val['product_class_id'];
            $arrStockData[$k]['quantity'] = $val['quantity'];
            ++$k;
        }

        // 受注詳細データの更新
        $objPurchase->registerOrderDetail($order_id, $arrDetail);

        // 在庫数調整
        if (ORDER_DELIV != $arrValues['status']
            && ORDER_CANCEL != $arrValues['status']) {
            foreach ($arrStockData AS $stock) {
                $objQuery->update('dtb_products_class', array(),
                                  'product_class_id = ?',
                                  array($stock['product_class_id']),
                                  array('stock' => 'stock + ?'),
                                  array($stock['quantity']));
            }
        }

        $arrAllShipping =  $objFormParam->getSwapArray(array('shipping_id',
                                                             'shipping_name01',
                                                             'shipping_name02',
                                                             'shipping_kana01',
                                                             'shipping_kana02',
                                                             'shipping_tel01',
                                                             'shipping_tel02',
                                                             'shipping_tel03',
                                                             'shipping_fax01',
                                                             'shipping_fax02',
                                                             'shipping_fax03',
                                                             'shipping_pref',
                                                             'shipping_zip01',
                                                             'shipping_zip02',
                                                             'shipping_addr01',
                                                             'shipping_addr02',
                                                             'shipping_date_year',
                                                             'shipping_date_month',
                                                             'shipping_date_day',
                                                             'time_id'));

        $arrAllShipmentItem =
            $objFormParam->getSwapArray(array('shipment_product_class_id',
                                              'shipment_product_code',
                                              'shipment_product_name',
                                              'shipment_classcategory_name1',
                                              'shipment_classcategory_name2',
                                              'shipment_price',
                                              'shipment_quantity'));

        $arrDelivTime = $objPurchase->getDelivTime($objFormParam->getValue('deliv_id'));

        $arrShippingValues = array();
        foreach ($arrAllShipping as $shipping_index => $arrShipping) {
            $shipping_id = $arrShipping['shipping_id'];
            $arrShippingValues[$shipping_index] = $arrShipping;

            $arrShippingValues[$shipping_index]['shipping_date']
                = SC_Utils_Ex::sfGetTimestamp($arrShipping['shipping_date_year'],
                                              $arrShipping['shipping_date_month'],
                                              $arrShipping['shipping_date_day']);

            // 配送業者IDを取得
            $arrShippingValues[$shipping_index]['deliv_id'] = $objFormParam->getValue('deliv_id');

            // お届け時間名称を取得
            $arrShippingValues[$shipping_index]['shipping_time'] = $arrDelivTime[$arrShipping['time_id']];

            // 複数配送の場合は配送商品を登録
            if (!SC_Utils_Ex::isBlank($arrAllShipmentItem)) {
                $arrShipmentValues = array();

                foreach ($arrAllShipmentItem[$shipping_index] as $key => $arrItem) {
                    $i = 0;
                    foreach ($arrItem as $item) {
                        $arrShipmentValues[$shipping_index][$i][str_replace('shipment_', '', $key)] = $item;
                        $i++;
                    }
                }
                $objPurchase->registerShipmentItem($order_id, $shipping_id,
                                                   $arrShipmentValues[$shipping_index]);
            }
        }
        $objPurchase->registerShipping($order_id, $arrShippingValues, false);
        $objQuery->commit();
        return $order_id;
    }

    /**
     * 受注商品の追加/更新を行う.
     *
     * 小画面で選択した受注商品をフォームに反映させる.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function doRegisterProduct(&$objFormParam) {
        $product_class_id = $objFormParam->getValue('add_product_class_id');
        if (SC_Utils_Ex::isBlank($product_class_id)) {
            $product_class_id = $objFormParam->getValue('edit_product_class_id');
            $changed_no = $objFormParam->getValue('no');
        }

        // 選択済みの商品であれば数量を1増やす
        $exists = false;
        $arrExistsProductClassIds = $objFormParam->getValue('product_class_id');
        foreach (array_keys($arrExistsProductClassIds) as $key) {
            $exists_product_class_id = $arrExistsProductClassIds[$key];
            if ($exists_product_class_id == $product_class_id) {
                $exists = true;
                $arrExistsQuantity = $objFormParam->getValue('quantity');
                $arrExistsQuantity[$key]++;
                $objFormParam->setValue('quantity', $arrExistsQuantity);
            }
        }

        // 新しく商品を追加した場合はフォームに登録
        // 商品を変更した場合は、該当行を変更
        if (!$exists) {
            $objProduct = new SC_Product_Ex();
            $arrProduct = $objProduct->getDetailAndProductsClass($product_class_id);
            $arrProduct['quantity'] = 1;
            $arrProduct['price'] = $arrProduct['price02'];
            $arrProduct['product_name'] = $arrProduct['name'];

            $arrUpdateKeys = array('product_id', 'product_class_id',
                                   'product_type_id', 'point_rate',
                                   'product_code', 'product_name',
                                   'classcategory_name1', 'classcategory_name2',
                                   'quantity', 'price');
            foreach ($arrUpdateKeys as $key) {
                $arrValues = $objFormParam->getValue($key);
                if (isset($changed_no)) {
                    $arrValues[$changed_no] = $arrProduct[$key];
                } else {
                    $arrValues[] = $arrProduct[$key];
                }
                $objFormParam->setValue($key, $arrValues);
            }
        } elseif (isset($changed_no)) {
            // 変更したが、選択済みの商品だった場合は、変更対象行を削除。
            $this->doDeleteProduct($changed_no, $objFormParam);
        }
    }

    /**
     * 受注商品を削除する.
     *
     * @param integer $delete_no 削除する受注商品の項番
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function doDeleteProduct($delete_no, &$objFormParam) {
        $arrDeleteKeys = array('product_id', 'product_class_id',
                               'product_type_id', 'point_rate',
                               'product_code', 'product_name',
                               'classcategory_name1', 'classcategory_name2',
                               'quantity', 'price');
        foreach ($arrDeleteKeys as $key) {
            $arrNewValues = array();
            $arrValues = $objFormParam->getValue($key);
            foreach ($arrValues as $index => $val) {
                if ($index != $delete_no) {
                    $arrNewValues[] = $val;
                }
            }
            $objFormParam->setValue($key, $arrNewValues);
        }
    }

    /**
     * お届け先を追加する.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function addShipping(&$objFormParam) {
        $objFormParam->setValue('shipping_quantity',
                                $objFormParam->getValue('shipping_quantity') + 1);
        $arrShippingIds = $objFormParam->getValue('shipping_id');
        $arrShippingIds[] = max($arrShippingIds) + 1;
        $objFormParam->setValue('shipping_id', $arrShippingIds);
    }

    /**
     * 顧客情報をフォームに設定する.
     *
     * @param integer $customer_id 顧客ID
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function setCustomerTo($customer_id, &$objFormParam) {
        $arrCustomer = SC_Helper_Customer_Ex::sfGetCustomerDataFromId($customer_id);
        foreach ($arrCustomer as $key => $val) {
            $objFormParam->setValue('order_' . $key, $val);
        }
        $objFormParam->setValue('customer_id', $customer_id);
        $objFormParam->setValue('customer_point', $arrCustomer['point']);
    }

    /**
     * アンカーキーを取得する.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return アンカーキーの文字列
     */
    function getAnchorKey(&$objFormParam) {
        $ancor_key = $objFormParam->getValue('anchor_key');
        if (!SC_Utils_Ex::isBlank($ancor_key)) {
            return "location.hash='#" . htmlentities(urlencode($ancor_key), ENT_QUOTES) . "'";
        }
        return '';
    }
}
?>
