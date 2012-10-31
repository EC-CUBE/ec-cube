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
 * 受注情報表示 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Order_Disp.php 20767 2011-03-22 10:07:32Z nanasess $
 */
class LC_Page_Admin_Order_Disp extends LC_Page_Admin_Order_Ex {

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
        'shipping_zip01',
        'shipping_zip02',
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
        $this->tpl_mainpage = 'order/disp.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subnavi = '';
        $this->tpl_subno = '';
        $this->tpl_subtitle = '受注情報表示';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrORDERSTATUS = $masterData->getMasterData('mtb_order_status');
        $this->arrDeviceType = $masterData->getMasterData('mtb_device_type');

        // 支払い方法の取得
        $this->arrPayment = SC_Helper_DB_Ex::sfGetIDValueList('dtb_payment', 'payment_id', 'payment_method');

        // 配送業者の取得
        $this->arrDeliv = SC_Helper_DB_Ex::sfGetIDValueList('dtb_deliv', 'deliv_id', 'name');
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
        $this->setOrderToFormParam($objFormParam, $order_id);

        $this->arrForm = $objFormParam->getFormParamList();
        $this->arrAllShipping = $objFormParam->getSwapArray(array_merge($this->arrShippingKeys, $this->arrShipmentItemKeys));
        $this->arrDelivTime = $objPurchase->getDelivTime($objFormParam->getValue('deliv_id'));
        $this->arrInfo = SC_Helper_DB_Ex::sfGetBasisData();

        $this->setTemplate($this->tpl_mainpage);

    }

    /**
     * デストラクタ.
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
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_MEMBER_NAME1'), 'order_name01', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_MEMBER_NAME2'), 'order_name02', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_MEMBER_KANA1'), 'order_kana01', STEXT_LEN, 'KVCa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_MEMBER_KANA2'), 'order_kana02', STEXT_LEN, 'KVCa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_EMAIL'), 'order_email', null, 'KVCa', array('NO_SPTAB', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_ZIP01'), 'order_zip01', ZIP01_LEN, 'n', array('NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_ZIP02'), 'order_zip02', ZIP02_LEN, 'n', array('NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_PREF'), 'order_pref', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_ADDR1'), 'order_addr01', MTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_ADDR2'), 'order_addr02', MTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_TEL1'), 'order_tel01', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_TEL2'), 'order_tel02', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_TEL3'), 'order_tel03', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));

        // 受注商品情報
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_DISCOUNT'), 'discount', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_DELIV_FEE'), 'deliv_fee', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CHARGE'), 'charge', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');

        // ポイント機能ON時のみ
        if (USE_POINT !== false) {
            $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_USE_POINT'), 'use_point', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        }

        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_DELIV'), 'deliv_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_PAYMENT_METHOD'), 'payment_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_ORDER_STATUS'), 'status', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_PAYMENT_METHOD_NAME'), 'payment_method');

        // 受注詳細情報
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_PRODUCT_TYPE_ID'), 'product_type_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_PRICE'), 'price', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_QUANTITY'), 'quantity', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_PRODUCT_ID'), 'product_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_PRODUCT_CLASS_ID'), 'product_class_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_POINT_RATE'), 'point_rate');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_PRODUCT_CODE'), 'product_code');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_PRODUCT_NAME'), 'product_name');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CLASS_NAME1'), 'classcategory_name1');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CLASS_NAME2'), 'classcategory_name2');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_NOTE'), 'note', MTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_DELETE_NO'), 'delete_no', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));

        // DB読込用
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_SUBTOTAL'), 'subtotal');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_TOTAL'), 'total');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_PAYMENT_TOTAL'), 'payment_total');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_ADD_POINT'), 'add_point');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BIRTH_POINT'), 'birth_point');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_TAX_TOTAL'), 'tax');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_TOTAL_POINT'), 'total_point');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_ID'), 'customer_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_ID'), 'edit_customer_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_NOW_POINT'), 'customer_point');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_BEFORE_POINT'), 'point');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_ORDER_NUMBER'), 'order_id');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_ORDER_DATE'), 'create_date');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_SHIPPING_DATE'), 'commit_date');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_REMARKS'), 'message');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_PAYMENT_DATE'), 'payment_date');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_DEVICE_TYPE'), 'device_type_id');

        // 複数情報
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_SHIPPING_QUANTITY'), 'shipping_quantity', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), 1);
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_SHIPPING_ID'), 'shipping_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), 0);
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_NAME1'), 'shipping_name01', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_NAME2'), 'shipping_name02', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_LASTKANA'), 'shipping_kana01', STEXT_LEN, 'KVCa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_FIRSTKANA'), 'shipping_kana02', STEXT_LEN, 'KVCa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_ZIP01'), 'shipping_zip01', ZIP01_LEN, 'n', array('NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_ZIP02'), 'shipping_zip02', ZIP02_LEN, 'n', array('NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_PREF'), 'shipping_pref', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_ADDR1'), 'shipping_addr01', MTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_ADDR2'), 'shipping_addr02', MTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_TEL1'), 'shipping_tel01', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_TEL2'), 'shipping_tel02', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_TEL3'), 'shipping_tel03', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_DELIV_TIME_ID'), 'time_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_DELIV_DATE_YEAR'), 'shipping_date_year', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_DELIV_DATE_MONTH'), 'shipping_date_month', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_DELIV_DATE_DAY'), 'shipping_date_day', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CUSTOMER_DELIV_DATE'), 'shipping_date', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_SHIPPING_PRODUCT_QUANTITY'), 'shipping_product_quantity', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));

        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_PRODUCT_CLASS_ID'), 'shipment_product_class_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_PRODUCT_CODE'), 'shipment_product_code');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_PRODUCT_NAME'), 'shipment_product_name');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CLASS_NAME1'), 'shipment_classcategory_name1');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_CLASS_NAME2'), 'shipment_classcategory_name2');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_PRICE'), 'shipment_price', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_QUANTITY'), 'shipment_quantity', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');

        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_PRODUCT_NO'), 'no', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_ADD_PRODUCT_CLASS_ID'), 'add_product_class_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_EDIT_PRODUCT_CLASS_ID'), 'edit_product_class_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(SC_I18n_Ex::t('PARAM_LABEL_ANCHOR_KEY'), 'anchor_key', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
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
         * $arrProductQuantity[$shipping_id] = 配送先ごとの配送商品数量
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

}
