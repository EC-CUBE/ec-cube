<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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

    var $arrShippingKeys = array(
        'shipping_id',
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
//        'shipping_zip01',
//        'shipping_zip02',
        'shipping_zipcode',
        'shipping_addr01',
        'shipping_addr02',
        'shipping_date_year',
        'shipping_date_month',
        'shipping_date_day',
        'time_id',
    );

    var $arrShipmentItemKeys = array(
        'shipment_product_class_id',
        'shipment_product_code',
        'shipment_product_name',
        'shipment_classcategory_name1',
        'shipment_classcategory_name2',
        'shipment_price',
        'shipment_quantity',
    );

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
        $this->tpl_mainno = 'order';
        $this->tpl_maintitle = t('TPL_MAINTITLE_001');
        $this->tpl_subtitle = t('LC_Page_Admin_Order_Edit_001');

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrORDERSTATUS = $masterData->getMasterData('mtb_order_status');
        $this->arrDeviceType = $masterData->getMasterData('mtb_device_type');

        $objDate = new SC_Date_Ex(RELEASE_YEAR);
        $this->arrYearShippingDate = $objDate->getYear('', date('Y'), '');
        $this->arrMonthShippingDate = $objDate->getMonth(true);
        $this->arrDayShippingDate = $objDate->getDay(true);

        // 支払い方法の取得
        $this->arrPayment = SC_Helper_DB_Ex::sfGetIDValueList('dtb_payment', 'payment_id', 'payment_method');

        // 配送業者の取得
        $this->arrDeliv = SC_Helper_DB_Ex::sfGetIDValueList('dtb_deliv', 'deliv_id', 'name');

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

        // パラメーター情報の初期化
        $this->lfInitParam($objFormParam);
        $objFormParam->setParam($_REQUEST);
        $objFormParam->convParam();
        $order_id = $objFormParam->getValue('order_id');
        $arrValuesBefore = array();

        // DBから受注情報を読み込む
        if (!SC_Utils_Ex::isBlank($order_id)) {
            $this->setOrderToFormParam($objFormParam, $order_id);
            $this->tpl_subno = 'index';
            $arrValuesBefore['payment_id'] = $objFormParam->getValue('payment_id');
            $arrValuesBefore['payment_method'] = $objFormParam->getValue('payment_method');
        } else {
            $this->tpl_subno = 'add';
            $this->tpl_mode = 'add';
            $arrValuesBefore['payment_id'] = NULL;
            $arrValuesBefore['payment_method'] = NULL;
            // お届け先情報を空情報で表示
            $arrShippingIds[] = null;
            $objFormParam->setValue('shipping_id', $arrShippingIds);

            // 新規受注登録で入力エラーがあった場合の画面表示用に、会員の現在ポイントを取得
            if (!SC_Utils_Ex::isBlank($objFormParam->getValue('customer_id'))) {
                $customer_id = $objFormParam->getValue('customer_id');
                $arrCustomer = SC_Helper_Customer_Ex::sfGetCustomerDataFromId($customer_id);
                $objFormParam->setValue('customer_point', $arrCustomer['point']);

                // 新規受注登録で、ポイント利用できるように現在ポイントを設定
                $objFormParam->setValue('point', $arrCustomer['point']);
            }
        }

        $this->arrSearchHidden = $objFormParam->getSearchArray();

        switch ($this->getMode()) {
            case 'pre_edit':
            case 'order_id':
                break;

            case 'edit':
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                $this->arrErr = $this->lfCheckError($objFormParam);
                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    $message = t('LC_Page_Admin_Order_Edit_003');
                    $order_id = $this->doRegister($order_id, $objPurchase, $objFormParam, $message, $arrValuesBefore);
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
                        $message = t('LC_Page_Admin_Order_Edit_002');
                        $order_id = $this->doRegister(null, $objPurchase, $objFormParam, $message, $arrValuesBefore);
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

            // 会員検索ポップアップより会員指定
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
                break;
        }

        $this->arrForm = $objFormParam->getFormParamList();
        $this->arrAllShipping = $objFormParam->getSwapArray(array_merge($this->arrShippingKeys, $this->arrShipmentItemKeys));
        $this->arrDelivTime = $objPurchase->getDelivTime($objFormParam->getValue('deliv_id'));
        $this->tpl_onload .= $this->getAnchorKey($objFormParam);
        $this->arrInfo = SC_Helper_DB_Ex::sfGetBasisData();
        if ($arrValuesBefore['payment_id'])
            $this->arrPayment[$arrValuesBefore['payment_id']] = $arrValuesBefore['payment_method'];

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
     * パラメーター情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {
        // 検索条件のパラメーターを初期化
        parent::lfInitParam($objFormParam);

        // お客様情報
        $objFormParam->addParam(t('c_Name of orderer (last name)_01'), 'order_name01', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Name of orderer (first name)_01'), 'order_name02', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_ORDER_LASTKANA'), 'order_kana01', STEXT_LEN, 'KVCa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_ORDER_FIRSTKANA'), 'order_kana02', STEXT_LEN, 'KVCa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_E-mail address_01'), 'order_email', null, 'KVCa', array('NO_SPTAB', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'));
//        $objFormParam->addParam(t('PARAM_LABEL_ZIP01'), 'order_zip01', ZIP01_LEN, 'n', array('NUM_CHECK', 'NUM_COUNT_CHECK'));
//        $objFormParam->addParam(t('PARAM_LABEL_ZIP02'), 'order_zip02', ZIP02_LEN, 'n', array('NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam(t('c_Postal code_01'), 'order_zipcode', ZIPCODE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Prefecture_01'), 'order_pref', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('c_Address 1_01'), 'order_addr01', MTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Address 2_01'), 'order_addr02', MTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Telephone number 1_01'), 'order_tel01', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Telephone number 2_01'), 'order_tel02', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Telephone number 3_01'), 'order_tel03', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Fax number 1_01'), 'order_fax01', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Fax number 2_01'), 'order_fax02', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Fax number 3_01'), 'order_fax03', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));

        // 受注商品情報
        $objFormParam->addParam(t('c_Discount_01'), 'discount', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam(t('c_Shipping fee_01'), 'deliv_fee', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam(t('c_Processing fee_01'), 'charge', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');

        // ポイント機能ON時のみ
        if (USE_POINT !== false) {
            $objFormParam->addParam(t('c_Used points_01'), 'use_point', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        }

        $objFormParam->addParam(t('PARAM_LABEL_DELIV'), 'deliv_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_CUSTOMER_PAYMENT_METHOD'), 'payment_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_ORDER_STATUS'), 'status', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_CUSTOMER_PAYMENT_METHOD_NAME'), 'payment_method');

        // 受注詳細情報
        $objFormParam->addParam(t('PARAM_LABEL_PRODUCT_TYPE_ID'), 'product_type_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam(t('c_Unit price_01'), 'price', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam(t('c_Quantity_01'), 'quantity', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam(t('PARAM_LABEL_PRODUCT_ID'), 'product_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam(t('PARAM_LABEL_PRODUCT_CLASS_ID'), 'product_class_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam(t('PARAM_LABEL_POINT_RATE'), 'point_rate');
        $objFormParam->addParam(t('c_Product code_01'), 'product_code');
        $objFormParam->addParam(t('c_Product name_01'), 'product_name');
        $objFormParam->addParam(t('PARAM_LABEL_CLASS_NAME1'), 'classcategory_name1');
        $objFormParam->addParam(t('PARAM_LABEL_CLASS_NAME2'), 'classcategory_name2');
        $objFormParam->addParam(t('PARAM_LABEL_NOTE'), 'note', MTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_DELETE_NO'), 'delete_no', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));

        // DB読込用
        $objFormParam->addParam(t('PARAM_LABEL_SUBTOTAL'), 'subtotal');
        $objFormParam->addParam(t('c_Total_01'), 'total');
        $objFormParam->addParam(t('PARAM_LABEL_PAYMENT_TOTAL'), 'payment_total');
        $objFormParam->addParam(t('c_Points added_01'), 'add_point');
        $objFormParam->addParam(t('PARAM_LABEL_BIRTH_POINT'), 'birth_point', null, 'n', array(), 0);
        $objFormParam->addParam(t('PARAM_LABEL_TAX_TOTAL'), 'tax');
        $objFormParam->addParam(t('PARAM_LABEL_TOTAL_POINT'), 'total_point');
        $objFormParam->addParam(t('PARAM_LABEL_CUSTOMER_ID'), 'customer_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam(t('PARAM_LABEL_CUSTOMER_ID'), 'edit_customer_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam(t('PARAM_LABEL_NOW_POINT'), 'customer_point');
        $objFormParam->addParam(t('PARAM_LABEL_BEFORE_POINT'), 'point');
        $objFormParam->addParam(t('PARAM_LABEL_ORDER_NUMBER'), 'order_id');
        $objFormParam->addParam(t('PARAM_LABEL_ORDER_DATE'), 'create_date');
        $objFormParam->addParam(t('PARAM_LABEL_SHIPPING_DATE'), 'commit_date');
        $objFormParam->addParam(t('PARAM_LABEL_REMARKS'), 'message');
        $objFormParam->addParam(t('PARAM_LABEL_PAYMENT_DATE'), 'payment_date');
        $objFormParam->addParam(t('PARAM_LABEL_DEVICE_TYPE'), 'device_type_id');

        // 複数情報
        $objFormParam->addParam(t('PARAM_LABEL_SHIPPING_QUANTITY'), 'shipping_quantity', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), 1);
        $objFormParam->addParam(t('PARAM_LABEL_SHIPPING_ID'), 'shipping_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), 0);
        $objFormParam->addParam(t('c_Name (last name)_01'), 'shipping_name01', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Name (first name)_01'), 'shipping_name02', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_CUSTOMER_LASTKANA'), 'shipping_kana01', STEXT_LEN, 'KVCa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_CUSTOMER_FIRSTKANA'), 'shipping_kana02', STEXT_LEN, 'KVCa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
//        $objFormParam->addParam(t('PARAM_LABEL_ZIP01'), 'shipping_zip01', ZIP01_LEN, 'n', array('NUM_CHECK', 'NUM_COUNT_CHECK'));
//        $objFormParam->addParam(t('PARAM_LABEL_ZIP02'), 'shipping_zip02', ZIP02_LEN, 'n', array('NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam(t('c_Postal code_01'), 'shipping_zipcode', ZIPCODE_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Prefecture_01'), 'shipping_pref', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('c_Address 1_01'), 'shipping_addr01', MTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Address 2_01'), 'shipping_addr02', MTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Telephone number 1_01'), 'shipping_tel01', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Telephone number 2_01'), 'shipping_tel02', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Telephone number 3_01'), 'shipping_tel03', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Fax number 1_01'), 'shipping_fax01', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Fax number 2_01'), 'shipping_fax02', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Fax number 3_01'), 'shipping_fax03', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_CUSTOMER_DELIV_TIME_ID'), 'time_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_CUSTOMER_DELIV_DATE_YEAR'), 'shipping_date_year', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_CUSTOMER_DELIV_DATE_MONTH'), 'shipping_date_month', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_CUSTOMER_DELIV_DATE_DAY'), 'shipping_date_day', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_CUSTOMER_DELIV_DATE'), 'shipping_date', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_SHIPPING_PRODUCT_QUANTITY'), 'shipping_product_quantity', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));

        $objFormParam->addParam(t('PARAM_LABEL_PRODUCT_CLASS_ID'), 'shipment_product_class_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('c_Product code_01'), 'shipment_product_code');
        $objFormParam->addParam(t('c_Product name_01'), 'shipment_product_name');
        $objFormParam->addParam(t('PARAM_LABEL_CLASS_NAME1'), 'shipment_classcategory_name1');
        $objFormParam->addParam(t('PARAM_LABEL_CLASS_NAME2'), 'shipment_classcategory_name2');
        $objFormParam->addParam(t('c_Unit price_01'), 'shipment_price', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam(t('c_Quantity_01'), 'shipment_quantity', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');

        $objFormParam->addParam(t('PARAM_LABEL_PRODUCT_NO'), 'no', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_ADD_PRODUCT_CLASS_ID'), 'add_product_class_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_EDIT_PRODUCT_CLASS_ID'), 'edit_product_class_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('PARAM_LABEL_ANCHOR_KEY'), 'anchor_key', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     * 複数配送用フォームの初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitMultipleParam(&$objFormParam) {
        $objFormParam->addParam(t('PARAM_LABEL_PRODUCT_CLASS_ID'), 'multiple_product_class_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('c_Product code_01'), 'multiple_product_code', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), 1);
        $objFormParam->addParam(t('c_Product name_01'), 'multiple_product_name', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), 1);
        $objFormParam->addParam(t('PARAM_LABEL_CLASS1'), 'multiple_classcategory_name1', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), 1);
        $objFormParam->addParam(t('PARAM_LABEL_CLASS2'), 'multiple_classcategory_name2', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), 1);
        $objFormParam->addParam(t('c_Unit price_01'), 'multiple_price', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), 1);
        $objFormParam->addParam(t('c_Quantity_01'), 'multiple_quantity', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), 1);
        $objFormParam->addParam(t('PARAM_LABEL_CUSTOMER_DELIV_ADDRESSEE'), 'multiple_shipping_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
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
         * $arrShipmentItem[お届け先ID][商品規格ID]['shipment_(key)'] = 値
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
         * フォームのお届け先ごとの配列を生成
         *
         * $arrShipmentForm['(key)'][$shipping_id][$item_index] = 値
         * $arrProductQuantity[$shipping_id] = お届け先ごとの配送商品数量
         */
        $arrShipmentForm = array();
        $arrProductQuantity = array();
        $arrShippingIds = $objFormParam->getValue('shipping_id');
        foreach ($arrShippingIds as $shipping_id) {
            $item_index = 0;
            foreach ($arrShipmentItem[$shipping_id] as $product_class_id => $shipment_item) {
                foreach ($shipment_item as $key => $val) {
                    $arrShipmentForm[$key][$shipping_id][$item_index] = $val;
                }
                // 受注商品の数量を設定
                $arrQuantity[$product_class_id] += $shipment_item['shipment_quantity'];
                $item_index++;
            }
            // お届け先ごとの配送商品数量を設定
            $arrProductQuantity[$shipping_id] = count($arrShipmentItem[$shipping_id]);
        }

        $objFormParam->setParam($arrShipmentForm);
        $objFormParam->setValue('shipping_product_quantity', $arrProductQuantity);

        // 受注商品の数量を変更
        $arrDest = array();
        foreach ($objFormParam->getValue('product_class_id') as $n => $order_product_class_id) {
            $arrDest['quantity'][$n] = 0;
        }
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

        $arrShippingsTmp = $objPurchase->getShippings($order_id);
        $arrShippings = array();
        foreach ($arrShippingsTmp as $row) {
            // お届け日の処理
            if (!SC_Utils_Ex::isBlank($row['shipping_date'])) {
                $ts = strtotime($row['shipping_date']);
                $row['shipping_date_year'] = date('Y', $ts);
                $row['shipping_date_month'] = date('n', $ts);
                $row['shipping_date_day'] = date('j', $ts);
            }
            $arrShippings[$row['shipping_id']] = $row;
        }
        $objFormParam->setValue('shipping_quantity', count($arrShippings));
        $objFormParam->setParam(SC_Utils_Ex::sfSwapArray($arrShippings));

        /*
         * 配送商品を設定
         *
         * $arrShipmentItem['shipment_(key)'][$shipping_id][$item_index] = 値
         * $arrProductQuantity[$shipping_id] = お届け先ごとの配送商品数量
         */
        $arrProductQuantity = array();
        $arrShipmentItem = array();
        foreach ($arrShippings as $shipping_id => $arrShipping) {
            $arrProductQuantity[$shipping_id] = count($arrShipping['shipment_item']);
            foreach ($arrShipping['shipment_item'] as $item_index => $arrItem) {
                foreach ($arrItem as $item_key => $item_val) {
                    $arrShipmentItem['shipment_' . $item_key][$shipping_id][$item_index] = $item_val;
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

        // ポイントを設定
        list($db_point, $rollback_point) = SC_Helper_DB_Ex::sfGetRollbackPoint(
            $order_id, $arrOrder['use_point'], $arrOrder['add_point'], $arrOrder['status']
        );
        $objFormParam->setValue('total_point', $db_point);
        $objFormParam->setValue('point', $rollback_point);

        if (!SC_Utils_Ex::isBlank($objFormParam->getValue('customer_id'))) {
            $arrCustomer = SC_Helper_Customer_Ex::sfGetCustomerDataFromId($objFormParam->getValue('customer_id'));
            $objFormParam->setValue('customer_point', $arrCustomer['point']);
        }
    }

    /**
     * 入力内容のチェックを行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return array エラーメッセージの配列
     */
    function lfCheckError(&$objFormParam) {
        $objProduct = new SC_Product_Ex();

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
        for ($i = 0; $i < $max; $i++) {
            // 小計の計算
            $subtotal += SC_Helper_DB_Ex::sfCalcIncTax($arrValues['price'][$i]) * $arrValues['quantity'][$i];
            // 小計の計算
            $totaltax += SC_Helper_DB_Ex::sfTax($arrValues['price'][$i]) * $arrValues['quantity'][$i];
            // 加算ポイントの計算
            $totalpoint += SC_Utils_Ex::sfPrePoint($arrValues['price'][$i], $arrValues['point_rate'][$i]) * $arrValues['quantity'][$i];

            // 在庫数のチェック
            $arrProduct = $objProduct->getDetailAndProductsClass($arrValues['product_class_id'][$i]);

            // 編集前の値と比較するため受注詳細を取得
            $objPurchase = new SC_Helper_Purchase_Ex();
            $arrOrderDetail = SC_Utils_Ex::sfSwapArray($objPurchase->getOrderDetail($objFormParam->getValue('order_id'), false));

            if ($arrProduct['stock_unlimited'] != '1'
                && $arrProduct['stock'] < $arrValues['quantity'][$i] - $arrOrderDetail['quantity'][$i]) {
                $class_name1 = $arrValues['classcategory_name1'][$i];
                $class_name1 = SC_Utils_Ex::isBlank($class_name1) ? t('LC_Page_Admin_Order_Edit_004') : $class_name1;
                $class_name2 = $arrValues['classcategory_name2'][$i];
                $class_name2 = SC_Utils_Ex::isBlank($class_name2) ? t('LC_Page_Admin_Order_Edit_004') : $class_name2;                
                $arrErr['quantity'][$i] .= t('LC_Page_Admin_Order_Edit_005', array('T_FIELD1' => $arrValues['product_name'][$i], 'T_FIELD2' => $class_name1,'T_FIELD3' => $class_name2, 'T_FIELD4' => ($arrOrderDetail['quantity'][$i] + $arrProduct['stock'])));
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
        $arrValues['total_point'] = $objFormParam->getValue('point') - $arrValues['use_point'];

        if ($arrValues['total'] < 0) {
            $arrErr['total'] = t('LC_Page_Admin_Order_Edit_006');
        }

        if ($arrValues['payment_total'] < 0) {
            $arrErr['payment_total'] = t('LC_Page_Admin_Order_Edit_007');
        }

        if ($arrValues['total_point'] < 0) {
            $arrErr['use_point'] = t('LC_Page_Admin_Order_Edit_008');
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
     * @param array $arrValuesBefore 更新前の受注情報
     * @return integer $order_id 受注ID
     *
     * エラー発生時は負数を返す。
     */
    function doRegister($order_id, &$objPurchase, &$objFormParam, &$message, &$arrValuesBefore) {

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrValues = $objFormParam->getDbArray();

        $where = 'order_id = ?';

        $objQuery->begin();

        // 支払い方法が変更されたら、支払い方法名称も更新
        if ($arrValues['payment_id'] != $arrValuesBefore['payment_id']) {
            $arrValues['payment_method'] = $this->arrPayment[$arrValues['payment_id']];
            $arrValuesBefore['payment_id'] = NULL;
        }

        // 受注テーブルの更新
        $order_id = $objPurchase->registerOrder($order_id, $arrValues);

        $arrDetail = $objFormParam->getSwapArray(array(
                'product_id',
                'product_class_id',
                'product_code',
                'product_name',
                'price', 'quantity',
                'point_rate',
                'classcategory_name1',
                'classcategory_name2',
        ));

        // 変更しようとしている商品情報とDBに登録してある商品情報を比較することで、更新すべき数量を計算
        $max = count($arrDetail);
        $k = 0;
        $arrStockData = array();
        for ($i = 0; $i < $max; $i++) {
            if (!empty($arrDetail[$i]['product_id'])) {
                $arrPreDetail = $objQuery->select('*', 'dtb_order_detail', 'order_id = ? AND product_class_id = ?', array($order_id, $arrDetail[$i]['product_class_id']));
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
                $objQuery->delete('dtb_order_detail', 'order_id = ? AND product_class_id = ?', array($order_id, $arrDetail[$i]['product_class_id']));
            }
        }

        // 上記の新しい商品のループでDELETEされなかった商品は、注文より削除された商品
        $arrPreDetail = $objQuery->select('*', 'dtb_order_detail', 'order_id = ?', array($order_id));
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

        $arrAllShipping = $objFormParam->getSwapArray($this->arrShippingKeys);
        $arrAllShipmentItem = $objFormParam->getSwapArray($this->arrShipmentItemKeys);

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
        // FXIME バリデーションを通さず $objFormParam の値で DB 問い合わせしている。(管理機能ため、さほど問題は無いと思うものの…)

        // 商品規格IDが指定されていない場合、例外エラーを発生
        if (strlen($product_class_id) === 0) {
            trigger_error('商品規格指定なし', E_USER_ERROR);
        }

        // 選択済みの商品であれば数量を1増やす
        $exists = false;
        $arrExistsProductClassIds = $objFormParam->getValue('product_class_id');
        foreach ($arrExistsProductClassIds as $key => $value) {
            $exists_product_class_id = $arrExistsProductClassIds[$key];
            if ($exists_product_class_id == $product_class_id) {
                $exists = true;
                $exists_no = $key;
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

            // 一致する商品規格がない場合、例外エラーを発生
            if (empty($arrProduct)) {
                trigger_error('商品規格一致なし', E_USER_ERROR);
            }

            $arrProduct['quantity'] = 1;
            $arrProduct['price'] = $arrProduct['price02'];
            $arrProduct['product_name'] = $arrProduct['name'];

            $arrUpdateKeys = array(
                'product_id', 'product_class_id', 'product_type_id', 'point_rate',
                'product_code', 'product_name', 'classcategory_name1', 'classcategory_name2',
                'quantity', 'price',
            );
            foreach ($arrUpdateKeys as $key) {
                $arrValues = $objFormParam->getValue($key);
                // FIXME getValueで文字列が返る場合があるので配列であるかをチェック
                if (!is_array($arrValues)) {
                    $arrValues = array();
                }

                if (isset($changed_no)) {
                    $arrValues[$changed_no] = $arrProduct[$key];
                } else {
                    $added_no = 0;
                    if (is_array($arrExistsProductClassIds)) {
                        $added_no = count($arrExistsProductClassIds);
                    }
                    $arrValues[$added_no] = $arrProduct[$key];
                }
                $objFormParam->setValue($key, $arrValues);
            }
        } elseif (isset($changed_no) && $exists_no != $changed_no) {
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
        $arrDeleteKeys = array(
            'product_id', 'product_class_id', 'product_type_id', 'point_rate',
            'product_code', 'product_name', 'classcategory_name1', 'classcategory_name2',
            'quantity', 'price',
        );
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
     * 会員情報をフォームに設定する.
     *
     * @param integer $customer_id 会員ID
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
