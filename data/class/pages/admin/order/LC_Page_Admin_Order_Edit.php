<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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

require_once CLASS_EX_REALDIR . 'page_extends/admin/order/LC_Page_Admin_Order_Ex.php';

/**
 * 受注修正 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Order_Edit extends LC_Page_Admin_Order_Ex
{
    public $arrShippingKeys = array(
        'shipping_id',
        'shipping_name01',
        'shipping_name02',
        'shipping_kana01',
        'shipping_kana02',
        'shipping_company_name',
        'shipping_tel01',
        'shipping_tel02',
        'shipping_tel03',
        'shipping_fax01',
        'shipping_fax02',
        'shipping_fax03',
        'shipping_pref',
        'shipping_country_id',
        'shipping_zipcode',
        'shipping_zip01',
        'shipping_zip02',
        'shipping_addr01',
        'shipping_addr02',
        'shipping_date_year',
        'shipping_date_month',
        'shipping_date_day',
        'time_id',
    );

    public $arrShipmentItemKeys = array(
        'shipment_product_class_id',
        'shipment_product_code',
        'shipment_product_name',
        'shipment_classcategory_name1',
        'shipment_classcategory_name2',
        'shipment_price',
        'shipment_quantity',
    );

    public $arrProductKeys = array(
        'product_id',
        'product_class_id',
        'product_type_id',
        'point_rate',
        'product_code',
        'product_name',
        'classcategory_name1',
        'classcategory_name2',
        'quantity',
        'price',
        'tax_rate',
        'tax_rule'
    );

    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'order/edit.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_maintitle = '受注管理';
        $this->tpl_subtitle = '受注登録';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrCountry = $masterData->getMasterData('mtb_country');
        $this->arrORDERSTATUS = $masterData->getMasterData('mtb_order_status');
        $this->arrDeviceType = $masterData->getMasterData('mtb_device_type');
        $this->arrSex = $masterData->getMasterData('mtb_sex');
        $this->arrJob = $masterData->getMasterData('mtb_job');

        $objShippingDate = new SC_Date_Ex(RELEASE_YEAR);
        $this->arrYearShippingDate = $objShippingDate->getYear('', date('Y'), '');
        $this->arrMonthShippingDate = $objShippingDate->getMonth(true);
        $this->arrDayShippingDate = $objShippingDate->getDay(true);

        $objBirthDate = new SC_Date_Ex(BIRTH_YEAR, date('Y',strtotime('now')));
        $this->arrBirthYear = $objBirthDate->getYear('', START_BIRTH_YEAR, '');
        $this->arrBirthMonth = $objBirthDate->getMonth(true);
        $this->arrBirthDay = $objBirthDate->getDay(true);

        // 支払い方法の取得
        $this->arrPayment = SC_Helper_Payment_Ex::getIDValueList();

        // 配送業者の取得
        $this->arrDeliv = SC_Helper_Delivery_Ex::getIDValueList();

        $this->arrInfo = SC_Helper_DB_Ex::sfGetBasisData();

        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
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
                //複数配送時に各商品の総量を設定
                $this->setProductsQuantity($objFormParam);
                $this->arrErr = $this->lfCheckError($objFormParam);
                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    $message = '受注を編集しました。';
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
                    //複数配送時に各商品の総量を設定
                    $this->setProductsQuantity($objFormParam);
                    $this->arrErr = $this->lfCheckError($objFormParam);
                    if (SC_Utils_Ex::isBlank($this->arrErr)) {
                        $message = '受注を登録しました。';
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
                //複数配送時に各商品の総量を設定
                $this->setProductsQuantity($objFormParam);
                $this->arrErr = $this->lfCheckError($objFormParam);
                break;

            // 商品削除
            case 'delete_product':
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                $delete_no = $objFormParam->getValue('delete_no');
                $this->doDeleteProduct($delete_no, $objFormParam);
                //複数配送時に各商品の総量を設定
                $this->setProductsQuantity($objFormParam);
                $this->arrErr = $this->lfCheckError($objFormParam);
                break;

            // 商品追加ポップアップより商品選択
            case 'select_product_detail':
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                $this->doRegisterProduct($objFormParam);
                //複数配送時に各商品の総量を設定
                $this->setProductsQuantity($objFormParam);
                $this->arrErr = $this->lfCheckError($objFormParam);
                break;

            // 会員検索ポップアップより会員指定
            case 'search_customer':
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                $customer_birth = $this->setCustomerTo($objFormParam->getValue('edit_customer_id'),
                                     $objFormParam);
                // 加算ポイントの計算
                if (USE_POINT === true && $this->tpl_mode == 'add') {
                    $birth_point = 0;
                    if ($customer_birth) {
                        $arrRet = preg_split('|[- :/]|', $customer_birth);
                        $birth_date = intval($arrRet[1]);
                        $now_date   = intval(date('m'));
                        // 誕生日月であった場合
                        if ($birth_date == $now_date) {
                            $birth_point = BIRTH_MONTH_POINT;
                        }
                    }
                    $objFormParam->setValue("birth_point",$birth_point);
                }
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

        $this->arrForm        = $objFormParam->getFormParamList();
        $this->arrAllShipping = $objFormParam->getSwapArray(array_merge($this->arrShippingKeys, $this->arrShipmentItemKeys));
        $this->top_shipping_id      = array_shift((array_keys($this->arrAllShipping)));
        $this->arrDelivTime   = SC_Helper_Delivery_Ex::getDelivTime($objFormParam->getValue('deliv_id'));
        $this->tpl_onload .= $this->getAnchorKey($objFormParam);
        if ($arrValuesBefore['payment_id'])
            $this->arrPayment[$arrValuesBefore['payment_id']] = $arrValuesBefore['payment_method'];
    }

    /**
     * パラメーター情報の初期化を行う.
     *
     * @param  SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    public function lfInitParam(&$objFormParam)
    {
        // 検索条件のパラメーターを初期化
        parent::lfInitParam($objFormParam);

        // お客様情報
        $objFormParam->addParam('注文者 お名前(姓)', 'order_name01', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'NO_SPTAB'));
        $objFormParam->addParam('注文者 お名前(名)', 'order_name02', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'NO_SPTAB'));
        $objFormParam->addParam('注文者 お名前(フリガナ・姓)', 'order_kana01', STEXT_LEN, 'KVCa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'NO_SPTAB'));
        $objFormParam->addParam('注文者 お名前(フリガナ・名)', 'order_kana02', STEXT_LEN, 'KVCa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'NO_SPTAB'));
        $objFormParam->addParam('注文者 会社名', 'order_company_name', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('メールアドレス', 'order_email', null, 'KVCa', array('NO_SPTAB', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'));
        $objFormParam->addParam('国', 'order_country_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ZIPCODE', 'order_zipcode', STEXT_LEN, 'n', array('GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('郵便番号1', 'order_zip01', ZIP01_LEN, 'n', array('NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam('郵便番号2', 'order_zip02', ZIP02_LEN, 'n', array('NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam('都道府県', 'order_pref', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('住所1', 'order_addr01', MTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('住所2', 'order_addr02', MTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('電話番号1', 'order_tel01', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('電話番号2', 'order_tel02', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('電話番号3', 'order_tel03', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('FAX番号1', 'order_fax01', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('FAX番号2', 'order_fax02', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('FAX番号3', 'order_fax03', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('性別', 'order_sex', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('職業', 'order_job', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('生年月日(年)', 'order_birth_year', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('生年月日(月)', 'order_birth_month', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('生年月日(日)', 'order_birth_day', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('生年月日', 'order_birth', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));

        // 受注商品情報
        $objFormParam->addParam('値引き', 'discount', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam('送料', 'deliv_fee', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam('手数料', 'charge', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');

        // ポイント機能ON時のみ
        if (USE_POINT !== false) {
            $objFormParam->addParam('利用ポイント', 'use_point', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        }

        $objFormParam->addParam('配送業者', 'deliv_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('お支払い方法', 'payment_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('対応状況', 'status', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('お支払方法名称', 'payment_method');

        // 受注詳細情報
        $objFormParam->addParam('商品種別ID', 'product_type_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam('単価', 'price', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam('数量', 'quantity', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam('商品ID', 'product_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam('商品規格ID', 'product_class_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam('ポイント付与率', 'point_rate');
        $objFormParam->addParam('商品コード', 'product_code');
        $objFormParam->addParam('商品名', 'product_name');
        $objFormParam->addParam('規格名1', 'classcategory_name1');
        $objFormParam->addParam('規格名2', 'classcategory_name2');
        $objFormParam->addParam('税率', 'tax_rate', INT_LEN, 'n', array('NUM_CHECK'));
        $objFormParam->addParam('課税規則', 'tax_rule', INT_LEN, 'n', array('NUM_CHECK'));
        $objFormParam->addParam('メモ', 'note', MTEXT_LEN, 'KVa', array('MAX_LENGTH_CHECK'));
        $objFormParam->addParam('削除用項番', 'delete_no', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));

        // DB読込用
        $objFormParam->addParam('小計', 'subtotal');
        $objFormParam->addParam('合計', 'total');
        $objFormParam->addParam('支払い合計', 'payment_total');
        $objFormParam->addParam('加算ポイント', 'add_point');
        $objFormParam->addParam('お誕生日ポイント', 'birth_point', null, 'n', array(), 0);
        $objFormParam->addParam('消費税合計', 'tax');
        $objFormParam->addParam('最終保持ポイント', 'total_point');
        $objFormParam->addParam('会員ID', 'customer_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam('会員ID', 'edit_customer_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam('現在のポイント', 'customer_point');
        $objFormParam->addParam('受注前ポイント', 'point');
        $objFormParam->addParam('注文番号', 'order_id');
        $objFormParam->addParam('受注日', 'create_date');
        $objFormParam->addParam('発送日', 'commit_date');
        $objFormParam->addParam('備考', 'message');
        $objFormParam->addParam('入金日', 'payment_date');
        $objFormParam->addParam('端末種別', 'device_type_id');
        $objFormParam->addParam('税率', 'order_tax_rate');
        $objFormParam->addParam('課税規則', 'order_tax_rule');

        // 複数情報
        $objFormParam->addParam('配送数', 'shipping_quantity', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), 1);
        $objFormParam->addParam('配送ID', 'shipping_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), 0);
        $objFormParam->addParam('お名前(姓)', 'shipping_name01', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'NO_SPTAB'));
        $objFormParam->addParam('お名前(名)', 'shipping_name02', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'NO_SPTAB'));
        $objFormParam->addParam('お名前(フリガナ・姓)', 'shipping_kana01', STEXT_LEN, 'KVCa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'NO_SPTAB'));
        $objFormParam->addParam('お名前(フリガナ・名)', 'shipping_kana02', STEXT_LEN, 'KVCa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK', 'NO_SPTAB'));
        $objFormParam->addParam('会社名', 'shipping_company_name', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('国', 'shipping_country_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('ZIPCODE', 'shipping_zipcode', STEXT_LEN, 'n', array('GRAPH_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('郵便番号1', 'shipping_zip01', ZIP01_LEN, 'n', array('NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam('郵便番号2', 'shipping_zip02', ZIP02_LEN, 'n', array('NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam('都道府県', 'shipping_pref', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('住所1', 'shipping_addr01', MTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('住所2', 'shipping_addr02', MTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam('電話番号1', 'shipping_tel01', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('電話番号2', 'shipping_tel02', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('電話番号3', 'shipping_tel03', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('FAX番号1', 'shipping_fax01', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('FAX番号2', 'shipping_fax02', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('FAX番号3', 'shipping_fax03', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam('お届け時間ID', 'time_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('お届け日(年)', 'shipping_date_year', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('お届け日(月)', 'shipping_date_month', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('お届け日(日)', 'shipping_date_day', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('お届け日', 'shipping_date', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));

        $objFormParam->addParam('商品規格ID', 'shipment_product_class_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('商品コード', 'shipment_product_code');
        $objFormParam->addParam('商品名', 'shipment_product_name');
        $objFormParam->addParam('規格名1', 'shipment_classcategory_name1');
        $objFormParam->addParam('規格名2', 'shipment_classcategory_name2');
        $objFormParam->addParam('単価', 'shipment_price', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');
        $objFormParam->addParam('数量', 'shipment_quantity', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), '0');

        $objFormParam->addParam('商品項番', 'no', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('追加商品規格ID', 'add_product_class_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('修正商品規格ID', 'edit_product_class_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('対象届け先ID', 'select_shipping_id', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('アンカーキー', 'anchor_key', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
    }

    /**
     * 複数配送用フォームの初期化を行う.
     *
     * @param  SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    public function lfInitMultipleParam(&$objFormParam)
    {
        $objFormParam->addParam('商品規格ID', 'multiple_product_class_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('商品コード', 'multiple_product_code', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), 1);
        $objFormParam->addParam('商品名', 'multiple_product_name', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), 1);
        $objFormParam->addParam('規格1', 'multiple_classcategory_name1', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), 1);
        $objFormParam->addParam('規格2', 'multiple_classcategory_name2', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), 1);
        $objFormParam->addParam('単価', 'multiple_price', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), 1);
        $objFormParam->addParam('数量', 'multiple_quantity', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'), 1);
        $objFormParam->addParam('お届け先', 'multiple_shipping_id', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
    }

    /**
     * 複数配送入力フォームで入力された値を SC_FormParam へ設定する.
     *
     * @param  SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    public function setMultipleItemTo(&$objFormParam)
    {
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
        }

        $objFormParam->setParam($arrShipmentForm);

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
     * @param  SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param  integer      $order_id     取得元の受注ID
     * @return void
     */
    public function setOrderToFormParam(&$objFormParam, $order_id)
    {
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
        $arrShipmentItem = array();
        foreach ($arrShippings as $shipping_id => $arrShipping) {
            foreach ($arrShipping['shipment_item'] as $item_index => $arrItem) {
                foreach ($arrItem as $item_key => $item_val) {
                    $arrShipmentItem['shipment_' . $item_key][$shipping_id][$item_index] = $item_val;
                }
            }
        }
        $objFormParam->setParam($arrShipmentItem);

        /*
         * 受注情報を設定
         * $arrOrderDetail と項目が重複しており, $arrOrderDetail は連想配列の値
         * が渡ってくるため, $arrOrder で上書きする.
         */
        $arrOrder = $objPurchase->getOrder($order_id);

        // 生年月日の処理
        if (!SC_Utils_Ex::isBlank($arrOrder['order_birth'])) {
            $order_birth = substr($arrOrder['order_birth'], 0, 10);
            $arrOrderBirth = explode("-", $order_birth);

            $arrOrder['order_birth_year'] = intval($arrOrderBirth[0]);
            $arrOrder['order_birth_month'] = intval($arrOrderBirth[1]);
            $arrOrder['order_birth_day'] = intval($arrOrderBirth[2]);
        }

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
     * @param  SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return array        エラーメッセージの配列
     */
    public function lfCheckError(&$objFormParam)
    {
        $objProduct = new SC_Product_Ex();
        $arrValues = $objFormParam->getHashArray();

        $arrErr = array();
        $arrErrTemp = $objFormParam->checkError();
        $arrErrDate = array();
        foreach ($arrValues['shipping_date_year'] as $key_index => $year) {
            $month = $arrValues['shipping_date_month'][$key_index];
            $day = $arrValues['shipping_date_day'][$key_index];
            $objError = new SC_CheckError_Ex(array('shipping_date_year' => $year,
                'shipping_date_month' => $month,
                'shipping_date_day' => $day));
            $objError->doFunc(array('お届け日', 'shipping_date_year', 'shipping_date_month', 'shipping_date_day'), array('CHECK_DATE'));
            $arrErrDate['shipping_date_year'][$key_index] = $objError->arrErr['shipping_date_year'];
        }
        $arrErrTemp = array_merge($arrErrTemp, $arrErrDate);

        // 商品の種類数
        $max = count($arrValues['quantity']);
        $subtotal = 0;
        $totalpoint = 0;
        $totaltax = 0;
        for ($i = 0; $i < $max; $i++) {
            // 小計の計算
            $subtotal += SC_Helper_DB_Ex::sfCalcIncTax($arrValues['price'][$i], $arrValues['tax_rate'][$i], $arrValues['tax_rule'][$i]) * $arrValues['quantity'][$i];
            // 小計の計算
            $totaltax += SC_Utils_Ex::sfTax($arrValues['price'][$i], $arrValues['tax_rate'][$i], $arrValues['tax_rule'][$i]) * $arrValues['quantity'][$i];
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
        $arrValues['add_point'] = SC_Helper_DB_Ex::sfGetAddPoint($totalpoint, $arrValues['use_point']) + $arrValues['birth_point'];

        // 最終保持ポイント
        $arrValues['total_point'] = $objFormParam->getValue('point') - $arrValues['use_point'];

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
        $arrErr = array_merge($arrErr, $arrErrTemp);

        return $arrErr;
    }

    /**
     * DB更新処理
     *
     * @param  integer            $order_id        受注ID
     * @param  SC_Helper_Purchase $objPurchase     SC_Helper_Purchase インスタンス
     * @param  SC_FormParam       $objFormParam    SC_FormParam インスタンス
     * @param  string             $message         通知メッセージ
     * @param  array              $arrValuesBefore 更新前の受注情報
     * @return integer            $order_id 受注ID
     *
     * エラー発生時は負数を返す。
     */
    public function doRegister($order_id, &$objPurchase, &$objFormParam, &$message, &$arrValuesBefore)
    {
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrValues = $objFormParam->getDbArray();

        $where = 'order_id = ?';

        $objQuery->begin();

        // 支払い方法が変更されたら、支払い方法名称も更新
        if ($arrValues['payment_id'] != $arrValuesBefore['payment_id']) {
            $arrValues['payment_method'] = $this->arrPayment[$arrValues['payment_id']];
            $arrValuesBefore['payment_id'] = NULL;
        }

        // 生年月日の調整
        $arrValues['order_birth'] = SC_Utils_Ex::sfGetTimestamp($arrValues['order_birth_year'], $arrValues['order_birth_month'], $arrValues['order_birth_day']);

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
                'tax_rate',
                'tax_rule'
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

        $arrDelivTime = SC_Helper_Delivery_Ex::getDelivTime($objFormParam->getValue('deliv_id'));
        //商品単価を複数配送にも適応
        $arrShippingValues = array();
        $arrIsNotQuantityUp = array();
        foreach ($arrAllShipping as $shipping_index => $arrShipping) {
            $shipping_id = $arrShipping['shipping_id'];
            $arrShippingValues[$shipping_index] = $arrShipping;

            $arrShippingValues[$shipping_index]['shipping_date']
                = SC_Utils_Ex::sfGetTimestamp($arrShipping['shipping_date_year'],
                                              $arrShipping['shipping_date_month'],
                                              $arrShipping['shipping_date_day']);

            //商品単価を複数配送にも反映する
            foreach ($arrDetail as $product_detail) {
                foreach ($arrAllShipmentItem[$shipping_index]['shipment_product_class_id'] as $relation_index => $shipment_product_class_id) {
                    if ($product_detail['product_class_id'] == $shipment_product_class_id) {
                        $arrAllShipmentItem[$shipping_index]['shipment_price'][$relation_index] = $product_detail['price'];
                    }
                }
            }
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
     * @param  SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    public function doRegisterProduct(&$objFormParam)
    {
        $product_class_id = $objFormParam->getValue('add_product_class_id');
        if (SC_Utils_Ex::isBlank($product_class_id)) {
            $product_class_id = $objFormParam->getValue('edit_product_class_id');
            $changed_no = $objFormParam->getValue('no');
            $this->shipmentEditProduct($objFormParam, $product_class_id, $changed_no);
        } else {
            $this->shipmentAddProduct($objFormParam, $product_class_id);
        }
    }

    /**
     * 受注商品を削除する.
     *
     * @param  integer      $delete_no    削除する受注商品の項番
     * @param  SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    public function doDeleteProduct($delete_no, &$objFormParam)
    {
        $select_shipping_id    = $objFormParam->getValue('select_shipping_id');

        //変更前のproduct_class_idが他の届け先にも存在するか
        $arrPreShipmentProductClassIds = $objFormParam->getValue('shipment_product_class_id');
        $arrPreProductClassIds         = $objFormParam->getValue('product_class_id');
        $delete_product_class_id       = $arrPreShipmentProductClassIds[$select_shipping_id][$delete_no];

        //配送先データ削除
        $arrNewShipments = $this->deleteShipment($objFormParam, $this->arrShipmentItemKeys , $select_shipping_id, $delete_no);
        $objFormParam->setParam($arrNewShipments);

        $is_product_delete = true;
        foreach ($arrNewShipments['shipment_product_class_id'] as $shipping_id => $arrShipmentProductClassIds) {
            foreach ($arrShipmentProductClassIds as $relation_index => $shipment_product_class_id) {
                if (in_array($delete_product_class_id, $arrShipmentProductClassIds)) {
                    $is_product_delete = false;
                    break;
                }
            }
        }

        //商品情報から削除
        if ($is_product_delete) {
            $this->checkDeleteProducts($objFormParam, $arrPreProductClassIds, $delete_product_class_id, $this->arrProductKeys);
        }
    }

    /**
     * お届け先を追加する.
     *
     * @param  SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    public function addShipping(&$objFormParam)
    {
        $objFormParam->setValue('shipping_quantity',
                                $objFormParam->getValue('shipping_quantity') + 1);
        $arrShippingIds = $objFormParam->getValue('shipping_id');
        $arrShippingIds[] = max($arrShippingIds) + 1;
        $objFormParam->setValue('shipping_id', $arrShippingIds);
    }

    /**
     * 会員情報をフォームに設定する.
     *
     * @param  integer      $customer_id  会員ID
     * @param  SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    public function setCustomerTo($customer_id, &$objFormParam)
    {
        $arrCustomer = SC_Helper_Customer_Ex::sfGetCustomerDataFromId($customer_id);
        foreach ($arrCustomer as $key => $val) {
            $objFormParam->setValue('order_' . $key, $val);
        }
        $objFormParam->setValue('customer_id', $customer_id);
        $objFormParam->setValue('customer_point', $arrCustomer['point']);

        return $arrCustomer['birth'];
    }

    /**
     * アンカーキーを取得する.
     *
     * @param  SC_FormParam                   $objFormParam SC_FormParam インスタンス
     * @return アンカーキーの文字列
     */
    public function getAnchorKey(&$objFormParam)
    {
        $ancor_key = $objFormParam->getValue('anchor_key');
        if (!SC_Utils_Ex::isBlank($ancor_key)) {
            return "location.hash='#" . htmlentities(urlencode($ancor_key), ENT_QUOTES) . "'";
        }

        return '';
    }

    /**
     * 商品を追加
     *
     * @param  SC_FormParam $objFormParam         SC_FormParam インスタンス
     * @param  integer      $add_product_class_id 追加商品規格ID
     * @return void
     */
    public function shipmentAddProduct(&$objFormParam, $add_product_class_id)
    {
        //複数配送に商品情報追加
        $select_shipping_id = $objFormParam->getValue('select_shipping_id');

        //届け先に選択済みの商品がある場合
        $arrShipmentProducts = $this->getShipmentProducts($objFormParam);

        if($arrShipmentProducts['shipment_product_class_id'] && in_array($add_product_class_id, $arrShipmentProducts['shipment_product_class_id'][$select_shipping_id])){
            foreach ($arrShipmentProducts['shipment_product_class_id'][$select_shipping_id] as $relation_index => $shipment_product_class_id) {
                if ($shipment_product_class_id == $add_product_class_id) {
                    $arrShipmentProducts['shipment_quantity'][$select_shipping_id][$relation_index]++;
                    break;
                }
            }
        } else {
            //届け先に選択商品がない場合
            $objProduct = new SC_Product_Ex();
            $arrAddProductInfo = $objProduct->getDetailAndProductsClass($add_product_class_id);

            $arrShipmentProducts['shipment_product_class_id'][$select_shipping_id][] = $add_product_class_id;
            $arrShipmentProducts['shipment_product_code'][$select_shipping_id][]     = $arrAddProductInfo['product_code'];
            $arrShipmentProducts['shipment_product_name'][$select_shipping_id][]     = $arrAddProductInfo['name'];
            $arrShipmentProducts['shipment_price'][$select_shipping_id][]            = $arrAddProductInfo['price02'];
            $arrShipmentProducts['shipment_quantity'][$select_shipping_id][]         = 1;

            //受注商品情報に追加
            $arrPreProductClassIds = $objFormParam->getValue('product_class_id');
            $arrProducts = $this->checkInsertOrderProducts($objFormParam, $arrPreProductClassIds, $add_product_class_id, $arrAddProductInfo);
            $objFormParam->setParam($arrProducts);
        }
        $objFormParam->setParam($arrShipmentProducts);
    }

    /**
     * 商品を変更
     *
     * @param  SC_FormParam $objFormParam         SC_FormParam インスタンス
     * @param  integer      $add_product_class_id 変更商品規格ID
     * @param  integer      $change_no            変更対象
     * @return void
     */
    public function shipmentEditProduct(&$objFormParam, $edit_product_class_id, $change_no)
    {
        $arrPreProductClassIds = $objFormParam->getValue('product_class_id');
        $select_shipping_id    = $objFormParam->getValue('select_shipping_id');

        $arrShipmentProducts = $this->getShipmentProducts($objFormParam);

        //既にあるデータは１つだけ数量を１増やす
        $pre_shipment_product_class_id = $arrShipmentProducts['shipment_product_class_id'][$select_shipping_id][$change_no];
        if ($pre_shipment_product_class_id == $edit_product_class_id) {
            $arrShipmentProducts['shipment_quantity'][$select_shipping_id][$change_no] ++;
        } elseif (in_array($edit_product_class_id, $arrShipmentProducts['shipment_product_class_id'][$select_shipping_id])) {
            //配送先データ削除
            $arrShipmentProducts = $this->deleteShipment($objFormParam, $this->arrShipmentItemKeys , $select_shipping_id, $change_no);
            foreach ($arrShipmentProducts['shipment_product_class_id'][$select_shipping_id] as $relation_index => $shipment_product_class_id) {
                if ($shipment_product_class_id == $edit_product_class_id) {
                    $arrShipmentProducts['shipment_quantity'][$select_shipping_id][$relation_index] ++;
                    break;
                }
            }
        } else {
            $objProduct = new SC_Product_Ex();
            $arrAddProductInfo = $objProduct->getDetailAndProductsClass($edit_product_class_id);

            //上書き
            $this->changeShipmentProducts($arrShipmentProducts, $arrAddProductInfo, $select_shipping_id, $change_no);
            //受注商品情報に追加
            $arrProducts = $this->checkInsertOrderProducts($objFormParam, $arrPreProductClassIds, $edit_product_class_id, $arrAddProductInfo);
            $objFormParam->setParam($arrProducts);
        }
        $objFormParam->setParam($arrShipmentProducts);

        //更新のみの場合、全配列を持っていないので、新しい配列を取得
        $arrNewShipmentProducts = $this->getShipmentProducts($objFormParam);
        $is_product_delete = true;
        //変更前のproduct_class_idが他の届け先にも存在するか
        foreach ($arrNewShipmentProducts['shipment_product_class_id'] as $shipping_id => $arrShipmentProductClassIds) {
            if (in_array($pre_shipment_product_class_id, $arrShipmentProductClassIds)) {
                $is_product_delete = false;
                break;
            }
        }

        //商品情報から削除
        if ($is_product_delete) {
            $this->checkDeleteProducts($objFormParam, $arrPreProductClassIds, $pre_shipment_product_class_id, $this->arrProductKeys);
        }
    }

    /**
     * 複数配送のパラメータを取り出す
     *
     * @param  SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return array        配送データ
     */
    public function getShipmentProducts(&$objFormParam)
    {
        $arrShipmentProducts['shipment_product_class_id']    = $objFormParam->getValue('shipment_product_class_id');
        $arrShipmentProducts['shipment_product_code']        = $objFormParam->getValue('shipment_product_code');
        $arrShipmentProducts['shipment_product_name']        = $objFormParam->getValue('shipment_product_name');
        $arrShipmentProducts['shipment_classcategory_name1'] = $objFormParam->getValue('shipment_classcategory_name1');
        $arrShipmentProducts['shipment_classcategory_name2'] = $objFormParam->getValue('shipment_classcategory_name2');
        $arrShipmentProducts['shipment_price']               = $objFormParam->getValue('shipment_price');
        $arrShipmentProducts['shipment_quantity']            = $objFormParam->getValue('shipment_quantity');

        foreach ($arrShipmentProducts as $key => $value) {
            if (!is_array($value)) {
                $arrShipmentProducts[$key] = array();
            }
        }

        return $arrShipmentProducts;
    }

    /**
     * 変更対象のデータを上書きする
     *
     * @param  array   $arrShipmentProducts 変更対象配列
     * @param  array   $arrProductInfo      上書きデータ
     * @param  integer $shipping_id         配送先ID
     * @param  array   $no                  変更対象
     * @return void
     */
    public function changeShipmentProducts(&$arrShipmentProducts, $arrProductInfo, $shipping_id, $no)
    {
        $arrShipmentProducts['shipment_product_class_id'][$shipping_id][$no]    = $arrProductInfo['product_class_id'];
        $arrShipmentProducts['shipment_product_code'][$shipping_id][$no]        = $arrProductInfo['product_code'];
        $arrShipmentProducts['shipment_product_name'][$shipping_id][$no]        = $arrProductInfo['name'];
        $arrShipmentProducts['shipment_classcategory_name1'][$shipping_id][$no] = $arrProductInfo['classcategory_name1'];
        $arrShipmentProducts['shipment_classcategory_name2'][$shipping_id][$no] = $arrProductInfo['classcategory_name2'];
        $arrShipmentProducts['shipment_price'][$shipping_id][$no]               = $arrProductInfo['price02'];
        $arrShipmentProducts['shipment_quantity'][$shipping_id][$no]            = 1;
    }

    /**
     * 商品側の総量計算&セット
     *
     * @param  SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    public function setProductsQuantity(&$objFormParam)
    {
        $arrShipmentsItems = $objFormParam->getSwapArray(array('shipment_product_class_id','shipment_quantity'));

        // 配送先が存在する時のみ、商品個数の再設定を行います
        if(!SC_Utils_Ex::isBlank($arrShipmentsItems)) {
            foreach ($arrShipmentsItems as $arritems) {
                foreach ($arritems['shipment_product_class_id'] as $relation_index => $shipment_product_class_id) {
                    $arrUpdateQuantity[$shipment_product_class_id] += $arritems['shipment_quantity'][$relation_index];
                }
            }

            $arrProductsClass = $objFormParam->getValue('product_class_id');
            $arrProductsQuantity = $objFormParam->getValue('quantity');
            foreach ($arrProductsClass as $relation_key => $product_class_id) {
                $arrQuantity['quantity'][$relation_key] = $arrUpdateQuantity[$product_class_id];
            }
            $objFormParam->setParam($arrQuantity);
        }
    }

    /**
     * 削除対象の確認、削除をする
     *
     * @param  SC_FormParam $objFormParam                               SC_FormParam インスタンス
     * @param  array        $arrProductClassIds　                      削除対象配列の商品規格ID
     * @param  integer      $delete_product_class_id　削除商品規? ?ID
     * @param  array        $arrDeleteKeys                              削除項目
     * @return void
     */
    public function checkDeleteProducts(&$objFormParam, $arrProductClassIds, $delete_product_class_id, $arrDeleteKeys)
    {
        foreach ($arrProductClassIds as $relation_index => $product_class_id) {
            //product_class_idの重複はないので、１つ削除したら完了
            if ($product_class_id == $delete_product_class_id) {
                foreach ($arrDeleteKeys as $delete_key) {
                    $arrProducts = $objFormParam->getValue($delete_key);
                    foreach ($arrProducts as $index => $product_info) {
                        if ($index != $relation_index) {
                            $arrUpdateParams[$delete_key][] = $product_info;
                        }
                    }
                    $objFormParam->setParam($arrUpdateParams);
                }
                break;
            }
        }
    }

    /**
     * 配送先商品の削除の削除
     *
     * @param  SC_FormParam $objFormParam          SC_FormParam インスタンス
     * @param  array        $arrShipmentDeleteKeys 削除項目
     * @param  integer      $delete_shipping_id　 削除配送ID
     * @param  array        $delete_no             削除対象
     * @return void
     */
    public function deleteShipment(&$objFormParam, $arrShipmentDeletKeys, $delete_shipping_id, $delete_no)
    {
            foreach ($arrShipmentDeletKeys as $delete_key) {
                $arrShipments = $objFormParam->getValue($delete_key);
                foreach ($arrShipments as $shipp_id => $arrKeyData) {
                    foreach ($arrKeyData as $relation_index => $shipment_info) {
                        if ($relation_index != $delete_no || $shipp_id != $delete_shipping_id) {
                            $arrUpdateParams[$delete_key][$shipp_id][] = $shipment_info;
                        }
                    }
                }
            }
            //$objFormParam->setParam($arrUpdateParams);
            return $arrUpdateParams;
        }

    /**
     * 受注商品一覧側に商品を追加
     *
     * @param  SC_FormParam $objFormParam                    SC_FormParam インスタンス
     * @param  array        $arrProductClassIds　           対象配列の商品規格ID
     * @param  integer      $indert_product_class_id　追?? 商品規格ID
     * @param  array        $arrAddProductInfo               追加データ
     * @return array        $arrAddProducts           更新データ
     */
    public function checkInsertOrderProducts(&$objFormParam, $arrProductClassIds, $insert_product_class_id, $arrAddProductInfo)
    {
        if(!$arrProductClassIds || !in_array($insert_product_class_id, $arrProductClassIds)){
            $arrAddProducts = array();

            $arrAddProductInfo['product_name'] = ($arrAddProductInfo['product_name'])?
                                                 $arrAddProductInfo['product_name']:$arrAddProductInfo['name'];
            $arrAddProductInfo['price']        = ($arrAddProductInfo['price'])?
                                                 $arrAddProductInfo['price']:$arrAddProductInfo['price02'];
            $arrAddProductInfo['quantity']     = 1;
            $arrAddProductInfo['tax_rate']     = ($objFormParam->getValue('order_tax_rate') == '')?
                                                 $this->arrInfo['tax']     :$objFormParam->getValue('order_tax_rate');
            $arrAddProductInfo['tax_rule']     = ($objFormParam->getValue('order_tax_rule') == '')?
                                                 $this->arrInfo['tax_rule']:$objFormParam->getValue('order_tax_rule');
            foreach ($this->arrProductKeys as $insert_key) {
                $value = $objFormParam->getValue($insert_key);
                $arrAddProducts[$insert_key]   = (is_array($value))? $value: array();
                $arrAddProducts[$insert_key][] = $arrAddProductInfo[$insert_key];
            }

            return $arrAddProducts;
        } else {
            //受注商品の数量は、複数配送側の集計で出しているので、重複しても数量を増やさない。
            return null;
        }
    }
}
