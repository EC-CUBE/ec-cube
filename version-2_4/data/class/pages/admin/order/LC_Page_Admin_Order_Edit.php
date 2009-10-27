<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
require_once(CLASS_PATH . "pages/LC_Page.php");

/* GMO決済モジュール連携用 */
if (file_exists(MODULE_PATH . 'mdl_gmopg/inc/include.php') === TRUE) {
  require_once(MODULE_PATH . 'mdl_gmopg/inc/include.php');
}

/* ペイジェント決済モジュール連携用 */
if (file_exists(MODULE_PATH . 'mdl_paygent/include.php') === TRUE) {
    require_once(MODULE_PATH . 'mdl_paygent/include.php');
}

/* F-REGI決済モジュール連携用 */
if (file_exists(MODULE_PATH. 'mdl_fregi/LC_Page_Mdl_Fregi_Config.php') === TRUE) {
    require_once(MODULE_PATH. 'mdl_fregi/LC_Page_Mdl_Fregi_Config.php');
}

/* SPS決済モジュール連携用 */
if (file_exists(MODULE_PATH . 'mdl_sps/request.php') === TRUE) {
    require_once(MODULE_PATH . 'mdl_sps/request.php');
}


/**
 * 受注修正 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Order_Edit extends LC_Page {

    // {{{ properties

    /** 表示モード */
    var $disp_mode;

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
        $this->tpl_subno = 'index';
        $this->tpl_subtitle = '受注管理';
        if (empty($_GET['order_id']) && empty($_POST['order_id'])) {
            $this->tpl_subno = 'add';
            $this->tpl_mode = 'add';
            $this->tpl_subtitle = '新規受注入力';
        }

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData("mtb_pref",
                                 array("pref_id", "pref_name", "rank"));
        $this->arrORDERSTATUS = $masterData->getMasterData("mtb_order_status");

        /* ペイジェント決済モジュール連携用 */
        if(function_exists("sfPaygentOrderPage")) {
            $this->arrDispKind = sfPaygentOrderPage();
        }

        /* F-REGI決済モジュール連携用 */
        if (file_exists(MODULE_PATH. 'mdl_fregi/LC_Page_Mdl_Fregi_Config.php') === TRUE) {
            global $arrFregiPayment;
            $this->arrFregiPayment = $arrFregiPayment;
            global $arrFregiDispKind;
            $this->arrFregiDispKind = $arrFregiDispKind;
        }
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $conn = new SC_DBConn();
        $objView = new SC_AdminView();
        $objSess = new SC_Session();
        $objSiteInfo = new SC_SiteInfo();
        $objDb = new SC_Helper_DB_Ex();
        $arrInfo = $objSiteInfo->data;

        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        // 検索パラメータの引き継ぎ
        foreach ($_POST as $key => $val) {
            if (ereg("^search_", $key)) {
                $this->arrSearchHidden[$key] = $val;
            }
        }

        // 表示モード判定
        if(isset($_GET['order_id']) &&
            SC_Utils_Ex::sfIsInt($_GET['order_id'])) {
            $this->disp_mode = true;
            $order_id = $_GET['order_id'];
        } else {
            $order_id = $_POST['order_id'];
        }
        $this->tpl_order_id = $order_id;

        // DBから受注情報を読み込む
        $this->lfGetOrderData($order_id);

        switch($_POST['mode']) {
        case 'pre_edit':
        case 'order_id':
            break;
        case 'edit':
        case 'add':
            // POST情報で上書き
            $this->objFormParam->setParam($_POST);

            // 入力値の変換
            $this->objFormParam->convParam();
            $this->arrErr = $this->lfCheckError();
            $this->arrErr = array_merge( (array) $this->arrErr, (array)$this->lfCheek($arrInfo, $_POST['mode']) );

            if(count($this->arrErr) == 0) {
                if ($_POST['mode'] == 'add') {
                    $order_id = $this->lfRegistNewData();

                    $this->tpl_order_id = $order_id;
                    $this->tpl_mode = 'edit';

                    $arrData['order_id'] = $order_id;
                    $this->objFormParam->setParam($arrData);

                    $text = "'新規受注を登録しました。'";
                } else {
                    $this->lfRegistData($_POST['order_id']);
                    $text = "'受注履歴を編集しました。'";
                }
                // DBから受注情報を再読込
                $this->lfGetOrderData($order_id);
                $this->tpl_onload = "window.alert(".$text.");";
            }
            break;
            // 再計算
        case 'cheek':
            // POST情報で上書き
            $this->objFormParam->setParam($_POST);
            // 入力値の変換
            $this->objFormParam->convParam();
            $this->arrErr = $this->lfCheckError();
            if(count($this->arrErr) == 0) {
                $this->arrErr = $this->lfCheek($arrInfo, $_POST['mode']);
            }
            break;
        /* ペイジェント決済モジュール連携用 */
        case 'paygent_order':
            $this->paygent_return = sfPaygentOrder($_POST['paygent_type'], $order_id);
            break;
        /* 商品削除*/
        case 'delete_product':
            $delete_no = $_POST['delete_no'];
            foreach ($_POST AS $key=>$val) {
                if (is_array($val)) {
                    foreach ($val AS $k=>$v) {
                        if ($k != $delete_no) {
                            $arrData[$key][] = $v;
                        }
                    }
                } else {
                    $arrData[$key] = $val;
                }
            }
            // 情報上書き
            $this->objFormParam->setParam($arrData);
            // 入力値の変換
            $this->objFormParam->convParam();
            $this->arrErr = $this->lfCheckError();
            if(count($this->arrErr) == 0) {
                $this->arrErr = $this->lfCheek($arrInfo, $_POST['mode']);
            }
            break;
        /* 商品追加ポップアップより商品選択後、商品情報取得*/
        case 'select_product_detail':
            // POST情報で上書き
            $this->objFormParam->setParam($_POST);
            if (!empty($_POST['add_product_id'])) {
                $this->lfInsertProduct($_POST['add_product_id'], $_POST['add_classcategory_id1'], $_POST['add_classcategory_id2']);
            } elseif (!empty($_POST['edit_product_id'])) {
                $this->lfUpdateProduct($_POST['edit_product_id'], $_POST['edit_classcategory_id1'], $_POST['edit_classcategory_id2'], $_POST['no']);
            }
            $arrData = $_POST;
            foreach ($this->arrForm AS $key=>$val) {
                if (is_array($val)) {
                    $arrData[$key] = $this->arrForm[$key]['value'];
                } else {
                    $arrData[$key] = $val;
                }
            }
            // 情報上書き
            $this->objFormParam->setParam($arrData);
            // 入力値の変換
            $this->objFormParam->convParam();
            $this->arrErr = $this->lfCheckError();
            if(count($this->arrErr) == 0) {
                $this->arrErr = $this->lfCheek($arrInfo, $_POST['mode']);
            }
            break;
        /* 顧客検索ポップアップより顧客指定後、顧客情報取得*/
        case 'search_customer':
            // POST情報で上書き
            $this->objFormParam->setParam($_POST);

            // 検索結果から顧客IDを指定された場合、顧客情報をフォームに代入する
            $this->lfSetCustomerInfo($_POST['edit_customer_id']);

            break;
        /* F-REGI決済モジュール連携用 */
        case 'fregi_status':
            $objFregiConfig = new LC_Page_Mdl_Fregi_Config();
            $this->fregi_err = $objFregiConfig->getSaleInfo($order_id, $this->arrDisp);
            $this->lfGetOrderData($order_id);
            break;
        case 'fregi_card':
            $objFregiConfig = new LC_Page_Mdl_Fregi_Config();
            $this->fregi_card_err = $objFregiConfig->setCardInfo($_POST['card_status'], $order_id, $this->arrDisp);
            $this->lfGetOrderData($order_id);
            break;
        /* SPS決済モジュール連携用 */
        case 'sps_request':
            $objErr = new SC_CheckError($_POST);
            $objErr->doFunc(array("年","sps_year"), array('EXIST_CHECK'));
            $objErr->doFunc(array("月","sps_month"), array('EXIST_CHECK'));
            $objErr->doFunc(array("日","sps_date"), array('EXIST_CHECK'));
            $objErr->doFunc(array("売上・返金日", "sps_year", "sps_month", "sps_date"), array("CHECK_DATE"));
            if ($objErr->arrErr) {
                $this->arrErr = $objErr->arrErr;
                break;
            }
            $sps_return = sfSpsRequest( $order_id, $_POST['request_type'] );
            // DBから受注情報を再読込
            $this->lfGetOrderData($order_id);
            $this->tpl_onload = "window.alert('".$sps_return."');";
            break;

        /* GMOPG連携用 */
        case 'gmopg_order_edit':
            require_once(MODULE_PATH . 'mdl_gmopg/class/LC_Mdl_GMOPG_OrderEdit.php');
            $objGMOOrderEdit = new LC_MDL_GMOPG_OrderEdit;
            $this->gmopg_order_edit_result = $objGMOOrderEdit->proccess();
            $this->lfGetOrderData($order_id);
            break;
        default:
            break;
        }

        // 支払い方法の取得
        $this->arrPayment = $objDb->sfGetIDValueList("dtb_payment", "payment_id", "payment_method");
        // 配送時間の取得
        $arrRet = $objDb->sfGetDelivTime($this->objFormParam->getValue('payment_id'));
        $this->arrDelivTime = SC_Utils_Ex::sfArrKeyValue($arrRet, 'time_id', 'deliv_time');

        $this->arrForm = $this->objFormParam->getFormParamList();
        $this->product_count = count($this->arrForm['quantity']['value']);

        // アンカーを設定
        if (isset($_POST['anchor_key']) && !empty($_POST['anchor_key'])) {
            $anchor_hash = "location.hash='#" . $_POST['anchor_key'] . "'";
        } else {
            $anchor_hash = "";
        }
        $this->tpl_onload .= $anchor_hash;

        $this->arrInfo = $arrInfo;

        /**
         * SPS決済 クレジット判定用処理
         */
        if (file_exists(MODULE_PATH . 'mdl_sps/request.php') === TRUE) {
            $objQuery = new SC_Query();
            $this->paymentType = $objQuery->getall("SELECT module_code, memo03 FROM dtb_payment WHERE payment_id = ? ", array($this->arrForm["payment_id"]['value']));
            $objDate = new SC_Date();
            $objDate->setStartYear(RELEASE_YEAR);
            $this->arrYear = $objDate->getYear();
            $this->arrMonth = $objDate->getMonth();
            $this->arrDay = $objDate->getDay();
        }

        $objView->assignobj($this);
        // 表示モード判定
        if(!$this->disp_mode) {
            $objView->display(MAIN_FRAME);
        } else {
            $objView->display('order/disp.tpl');
        }
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /* パラメータ情報の初期化 */
    function lfInitParam() {

        // お客様情報
        $this->objFormParam->addParam("顧客名1", "order_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("顧客名2", "order_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("顧客名カナ1", "order_kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("顧客名カナ2", "order_kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("メールアドレス", "order_email", MTEXT_LEN, "KVCa", array("EXIST_CHECK", "NO_SPTAB", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("郵便番号1", "order_zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $this->objFormParam->addParam("郵便番号2", "order_zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $this->objFormParam->addParam("都道府県", "order_pref", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("住所1", "order_addr01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("住所2", "order_addr02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("電話番号1", "order_tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $this->objFormParam->addParam("電話番号2", "order_tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $this->objFormParam->addParam("電話番号3", "order_tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));

        // 配送先情報
        $this->objFormParam->addParam("お名前1", "deliv_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("お名前2", "deliv_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("フリガナ1", "deliv_kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("フリガナ2", "deliv_kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("郵便番号1", "deliv_zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $this->objFormParam->addParam("郵便番号2", "deliv_zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $this->objFormParam->addParam("都道府県", "deliv_pref", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("住所1", "deliv_addr01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("住所2", "deliv_addr02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("電話番号1", "deliv_tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $this->objFormParam->addParam("電話番号2", "deliv_tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $this->objFormParam->addParam("電話番号3", "deliv_tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));


        // 受注商品情報
        $this->objFormParam->addParam("値引き", "discount", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
        $this->objFormParam->addParam("送料", "deliv_fee", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
        $this->objFormParam->addParam("手数料", "charge", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));

        // ポイント機能ON時のみ
        if( USE_POINT === true ){
            $this->objFormParam->addParam("利用ポイント", "use_point", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        }

        $this->objFormParam->addParam("お支払い方法", "payment_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("配送時間ID", "deliv_time_id", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("対応状況", "status", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("配達日", "deliv_date", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("お支払方法名称", "payment_method");
        $this->objFormParam->addParam("配送時間", "deliv_time");

        // 受注詳細情報
        $this->objFormParam->addParam("単価", "price", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
        $this->objFormParam->addParam("個数", "quantity", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
        $this->objFormParam->addParam("商品ID", "product_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"), '0');
        $this->objFormParam->addParam("ポイント付与率", "point_rate");
        $this->objFormParam->addParam("商品コード", "product_code");
        $this->objFormParam->addParam("商品名", "product_name");
        $this->objFormParam->addParam("規格1", "classcategory_id1");
        $this->objFormParam->addParam("規格2", "classcategory_id2");
        $this->objFormParam->addParam("規格名1", "classcategory_name1");
        $this->objFormParam->addParam("規格名2", "classcategory_name2");
        $this->objFormParam->addParam("メモ", "note", MTEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
        // DB読込用
        $this->objFormParam->addParam("小計", "subtotal");
        $this->objFormParam->addParam("合計", "total");
        $this->objFormParam->addParam("支払い合計", "payment_total");
        $this->objFormParam->addParam("加算ポイント", "add_point");
        $this->objFormParam->addParam("お誕生日ポイント", "birth_point");
        $this->objFormParam->addParam("消費税合計", "tax");
        $this->objFormParam->addParam("最終保持ポイント", "total_point");
        $this->objFormParam->addParam("顧客ID", "customer_id");
        $this->objFormParam->addParam("現在のポイント", "point");
        $this->objFormParam->addParam("注文番号", "order_id");
        $this->objFormParam->addParam("受注日", "create_date");
        $this->objFormParam->addParam("発送日", "commit_date");
    }

    function lfGetOrderData($order_id) {
        if(SC_Utils_Ex::sfIsInt($order_id)) {
            // DBから受注情報を読み込む
            $objQuery = new SC_Query();
            $objDb = new SC_Helper_DB_Ex();
            $where = "order_id = ?";
            $arrRet = $objQuery->select("*", "dtb_order", $where, array($order_id));
            $this->objFormParam->setParam($arrRet[0]);
            list($point, $total_point) = $objDb->sfGetCustomerPoint($order_id, $arrRet[0]['use_point'], $arrRet[0]['add_point']);
            $this->objFormParam->setValue('total_point', $total_point);
            $this->objFormParam->setValue('point', $point);
            $this->arrForm = $arrRet[0];
            // 受注詳細データの取得
            $arrRet = $this->lfGetOrderDetail($order_id);
            $arrRet = SC_Utils_Ex::sfSwapArray($arrRet);
            $this->arrForm = array_merge($this->arrForm, $arrRet);
            $this->objFormParam->setParam($arrRet);

            // その他支払い情報を表示
            if($this->arrForm["memo02"] != "") $this->arrForm["payment_info"] = unserialize($this->arrForm["memo02"]);
            if($this->arrForm["memo01"] == PAYMENT_CREDIT_ID){
                $this->arrForm["payment_type"] = "クレジット決済";
            }elseif($this->arrForm["memo01"] == PAYMENT_CONVENIENCE_ID){
                $this->arrForm["payment_type"] = "コンビニ決済";
            }else{
                $this->arrForm["payment_type"] = "お支払い";
            }
            //受注データを表示用配列に代入（各EC-CUBEバージョンと決済モジュールとのデータ連携保全のため）
            $this->arrDisp = $this->arrForm;
        }
    }

    // 受注詳細データの取得
    function lfGetOrderDetail($order_id) {
        $objQuery = new SC_Query();
        $col = "product_id, classcategory_id1, classcategory_id2, product_code, product_name, classcategory_name1, classcategory_name2, price, quantity, point_rate";
        $where = "order_id = ?";
        $objQuery->setorder("classcategory_id1, classcategory_id2");
        $arrRet = $objQuery->select($col, "dtb_order_detail", $where, array($order_id));
        return $arrRet;
    }

    /* 入力内容のチェック */
    function lfCheckError() {
        // 入力データを渡す。
        $arrRet =  $this->objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError();

        return $objErr->arrErr;
    }

    /* 計算処理 */
    function lfCheek($arrInfo,$mode = "") {
        $objDb = new SC_Helper_DB_Ex();
        $arrVal = $this->objFormParam->getHashArray();
        $arrErr = array();

        // 商品の種類数
        $max = count($arrVal['quantity']);
        $subtotal = 0;
        $totalpoint = 0;
        $totaltax = 0;
        for($i = 0; $i < $max; $i++) {
            // 小計の計算
            $subtotal += SC_Utils_Ex::sfPreTax($arrVal['price'][$i], $arrInfo['tax'], $arrInfo['tax_rule']) * $arrVal['quantity'][$i];
            // 小計の計算
            $totaltax += SC_Utils_Ex::sfTax($arrVal['price'][$i], $arrInfo['tax'], $arrInfo['tax_rule']) * $arrVal['quantity'][$i];
            // 加算ポイントの計算
            $totalpoint += SC_Utils_Ex::sfPrePoint($arrVal['price'][$i], $arrVal['point_rate'][$i]) * $arrVal['quantity'][$i];
        }

        // 消費税
        $arrVal['tax'] = $totaltax;
        // 小計
        $arrVal['subtotal'] = $subtotal;
        // 合計
        $arrVal['total'] = $subtotal - $arrVal['discount'] + $arrVal['deliv_fee'] + $arrVal['charge'];
        // お支払い合計
        $arrVal['payment_total'] = $arrVal['total'] - ($arrVal['use_point'] * POINT_VALUE);

        // 加算ポイント
        $arrVal['add_point'] = SC_Utils_Ex::sfGetAddPoint($totalpoint, $arrVal['use_point'], $arrInfo);

        if (strlen($_POST['customer_id']) >0){
            list($arrVal['point'], $arrVal['total_point']) = $objDb->sfGetCustomerPointFromCid($_POST['customer_id'], $arrVal['use_point'], $arrVal['add_point']);
        }else{
            list($arrVal['point'], $arrVal['total_point']) = $objDb->sfGetCustomerPoint($_POST['order_id'], $arrVal['use_point'], $arrVal['add_point']);
        }
        if($arrVal['total'] < 0) {
            $arrErr['total'] = '合計額がマイナス表示にならないように調整して下さい。<br />';
        }

        if($arrVal['payment_total'] < 0) {
            $arrErr['payment_total'] = 'お支払い合計額がマイナス表示にならないように調整して下さい。<br />';
        }
        //新規追加受注のみ
        if ($mode == "add"){
            if($arrVal['total_point'] < 0) {
                $arrErr['use_point'] = '最終保持ポイントがマイナス表示にならないように調整して下さい。<br />';
            }
        }

        $this->objFormParam->setParam($arrVal);
        return $arrErr;
    }

    function lfReCheek($arrData, $arrInfo) {
        // 情報上書き
        $this->objFormParam->setParam($arrData);
        // 入力値の変換
        $this->objFormParam->convParam();
        #if(count($this->arrErr) == 0) {
            $this->arrErr = $this->lfCheek($arrInfo);
        #}
    }
    /* DB登録処理 */
    function lfRegistData($order_id) {
        $objQuery = new SC_Query();

        $objQuery->begin();

        // 入力データを渡す。
        $arrRet =  $this->objFormParam->getHashArray();
        foreach($arrRet as $key => $val) {
            // 配列は登録しない
            if(!is_array($val)) {
                $sqlval[$key] = $val;
            }
        }

        unset($sqlval['total_point']);
        unset($sqlval['point']);

        $where = "order_id = ?";

        /*
         * XXX 本来なら配列だが, update 関数を string として
         *     チェックしているため...
         */
        if (!isset($addcol)) $addcol = "";

        // 受注テーブルの更新
        $objQuery->update("dtb_order", $sqlval, $where, array($order_id), $addcol);

        $sql = "";
        $sql .= " UPDATE";
        $sql .= "     dtb_order";
        $sql .= " SET";
        $sql .= "     payment_method = (SELECT payment_method FROM dtb_payment WHERE payment_id = ?)";
        $sql .= "     ,deliv_time = (SELECT deliv_time FROM dtb_delivtime WHERE time_id = ? AND deliv_id = (SELECT deliv_id FROM dtb_payment WHERE payment_id = ? ))";
        // 受注ステータスの判定
        if ($sqlval['status'] == ODERSTATUS_COMMIT) {
            // 受注テーブルの発送済み日を更新する
            $sql .= "     ,commit_date = NOW()";
        }
        $sql .= " WHERE order_id = ?";

        if ($arrRet['deliv_time_id'] == "") {
            $deliv_time_id = 0;
        }else{
            $deliv_time_id = $arrRet['deliv_time_id'];
        }
        $arrUpdData = array($arrRet['payment_id'], $deliv_time_id, $arrRet['payment_id'], $order_id);
        $objQuery->query($sql, $arrUpdData);

        // 受注詳細データの更新
        $arrDetail = $this->objFormParam->getSwapArray(array("product_id", "product_code", "product_name", "price", "quantity", "point_rate", "classcategory_id1", "classcategory_id2", "classcategory_name1", "classcategory_name2"));
        $objQuery->delete("dtb_order_detail", $where, array($order_id));


        $max = count($arrDetail);
        for($i = 0; $i < $max; $i++) {
            $sqlval = array();
            $sqlval['order_id'] = $order_id;
            $sqlval['product_id']  = $arrDetail[$i]['product_id'];
            $sqlval['product_code']  = $arrDetail[$i]['product_code'];
            $sqlval['product_name']  = $arrDetail[$i]['product_name'];
            $sqlval['price']  = $arrDetail[$i]['price'];
            $sqlval['quantity']  = $arrDetail[$i]['quantity'];
            $sqlval['point_rate']  = $arrDetail[$i]['point_rate'];
            $sqlval['classcategory_id1'] = $arrDetail[$i]['classcategory_id1'];
            $sqlval['classcategory_id2'] = $arrDetail[$i]['classcategory_id2'];
            $sqlval['classcategory_name1'] = $arrDetail[$i]['classcategory_name1'];
            $sqlval['classcategory_name2'] = $arrDetail[$i]['classcategory_name2'];
            $objQuery->insert("dtb_order_detail", $sqlval);
        }


        $objQuery->commit();
    }

    /* DB登録処理(追加) */
    function lfRegistNewData() {
        $objQuery = new SC_Query();

        $objQuery->begin();

        // 入力データを渡す。
        $arrRet =  $this->objFormParam->getHashArray();
        foreach($arrRet as $key => $val) {
            // 配列は登録しない
            if(!is_array($val)) {
                $sqlval[$key] = $val;
            }
        }

        // postgresqlとmysqlとで処理を分ける
        if (DB_TYPE == "pgsql") {
            $order_id = $objQuery->nextval("dtb_order","order_id");
        }elseif (DB_TYPE == "mysql") {
            $order_id = $objQuery->get_auto_increment("dtb_order");
        }

        $sqlval['order_id'] = $order_id;
        $sqlval['create_date'] = "Now()";

        // 注文ステータス:指定が無ければ新規受付に設定
        if($sqlval["status"] == ""){
            $sqlval['status'] = '1';
        }

        // customer_id
        if($sqlval["customer_id"] == ""){
            $sqlval['customer_id'] = '0';
        }

        unset($sqlval['total_point']);
        unset($sqlval['point']);

        $where = "order_id = ?";

        // 受注ステータスの判定
        if ($sqlval['status'] == ODERSTATUS_COMMIT) {
            // 受注テーブルの発送済み日を更新する
            $sqlval['commit_date'] = "Now()";
        }

        // 受注テーブルの登録
        $objQuery->insert("dtb_order", $sqlval);

        $sql = "";
        $sql .= " UPDATE";
        $sql .= "     dtb_order";
        $sql .= " SET";
        $sql .= "     payment_method = (SELECT payment_method FROM dtb_payment WHERE payment_id = ?)";
        $sql .= "     ,deliv_time = (SELECT deliv_time FROM dtb_delivtime WHERE time_id = ? AND deliv_id = (SELECT deliv_id FROM dtb_payment WHERE payment_id = ? ))";
        $sql .= " WHERE order_id = ?";

        if ($arrRet['deliv_time_id'] == "") {
            $deliv_time_id = 0;
        } else {
            $deliv_time_id = $arrRet['deliv_time_id'];
        }
        $arrUpdData = array($arrRet['payment_id'], $deliv_time_id, $arrRet['payment_id'], $order_id);
        $objQuery->query($sql, $arrUpdData);

        // 受注詳細データの更新
        $arrDetail = $this->objFormParam->getSwapArray(array("product_id", "product_code", "product_name", "price", "quantity", "point_rate", "classcategory_id1", "classcategory_id2", "classcategory_name1", "classcategory_name2"));
        $objQuery->delete("dtb_order_detail", $where, array($order_id));

        $max = count($arrDetail);
        for($i = 0; $i < $max; $i++) {
            $sqlval = array();
            $sqlval['order_id'] = $order_id;
            $sqlval['product_id']  = $arrDetail[$i]['product_id'];
            $sqlval['product_code']  = $arrDetail[$i]['product_code'];
            $sqlval['product_name']  = $arrDetail[$i]['product_name'];
            $sqlval['price']  = $arrDetail[$i]['price'];
            $sqlval['quantity']  = $arrDetail[$i]['quantity'];
            $sqlval['point_rate']  = $arrDetail[$i]['point_rate'];
            $sqlval['classcategory_id1'] = $arrDetail[$i]['classcategory_id1'];
            $sqlval['classcategory_id2'] = $arrDetail[$i]['classcategory_id2'];
            $sqlval['classcategory_name1'] = $arrDetail[$i]['classcategory_name1'];
            $sqlval['classcategory_name2'] = $arrDetail[$i]['classcategory_name2'];
            $objQuery->insert("dtb_order_detail", $sqlval);
        }
        $objQuery->commit();

        return $order_id;
    }

    function lfInsertProduct($product_id, $classcategory_id1, $classcategory_id2) {
        $arrProduct = $this->lfGetProductsClass($product_id, $classcategory_id1, $classcategory_id2);
        $this->arrForm = $this->objFormParam->getFormParamList();
        $existes = false;
        $existes_key = NULL;
        // 既に同じ商品がないか、確認する
        if (!empty($this->arrForm['product_id']['value'])) {
            foreach ($this->arrForm['product_id']['value'] AS $key=>$val) {
                if ($val == $product_id && $this->arrForm['product_id']['classcategory_id1'][$key] == $classcategory_id1 && $this->arrForm['product_id']['classcategory_id2'][$key] == $classcategory_id2) {
                    // 既に同じ商品がある
                    $existes = true;
                    $existes_key = $key;
                }
            }
        }

        if ($existes) {
            // 既に同じ商品がある場合
            ++$this->arrForm['quantity']['value'][$existes_key];
        } else {
            // 既に同じ商品がない場合
            $this->lfSetProductData($arrProduct);
        }
    }

    function lfUpdateProduct($product_id, $classcategory_id1, $classcategory_id2, $no) {
        $arrProduct = $this->lfGetProductsClass($product_id, $classcategory_id1, $classcategory_id2);
        $this->arrForm = $this->objFormParam->getFormParamList();
        $this->lfSetProductData($arrProduct, $no);
    }

    function lfSetProductData($arrProduct, $no = null) {
        foreach ($arrProduct AS $key=>$val) {
            if (!is_array($this->arrForm[$key]['value'])) {
                unset($this->arrForm[$key]['value']);
            }
            if ($no === null) {
                $this->arrForm[$key]['value'][] = $arrProduct[$key];
            } else {
                $this->arrForm[$key]['value'][$no] = $arrProduct[$key];
            }
        }
    }

    function lfGetProductsClass($product_id, $classcategory_id1, $classcategory_id2) {
        $objDb = new SC_Helper_DB_Ex();
        $arrClassCatName = $objDb->sfGetIDValueList("dtb_classcategory", "classcategory_id", "name");
        $arrRet = $objDb->sfGetProductsClass(array($product_id, $classcategory_id1, $classcategory_id2));

        $arrProduct['price'] = $arrRet['price02'];
        $arrProduct['quantity'] = 1;
        $arrProduct['product_id'] = $arrRet['product_id'];
        $arrProduct['point_rate'] = $arrRet['point_rate'];
        $arrProduct['product_code'] = $arrRet['product_code'];
        $arrProduct['product_name'] = $arrRet['name'];
        $arrProduct['classcategory_id1'] = $arrRet['classcategory_id1'];
        $arrProduct['classcategory_id2'] = $arrRet['classcategory_id2'];
        $arrProduct['classcategory_name1'] = $arrClassCatName[$arrRet['classcategory_id1']];
        $arrProduct['classcategory_name2'] = $arrClassCatName[$arrRet['classcategory_id2']];

        return $arrProduct;
    }

    /**
     * 検索結果から顧客IDを指定された場合、顧客情報をフォームに代入する
     * @param int $edit_customer_id 顧客ID
     */
    function lfSetCustomerInfo($edit_customer_id = ""){
        // 顧客IDが指定されている場合のみ、処理を実行する
        if( $edit_customer_id === "" ) return ;

        // 検索で選択された顧客IDが入力されている場合
        if( is_null($edit_customer_id) === false && 0 < strlen($edit_customer_id) && SC_Utils_Ex::sfIsInt($edit_customer_id) ){
            $objQuery = new SC_Query();

            // 顧客情報を取得する
            $arrCustomerInfo = $objQuery->select('*', 'dtb_customer', 'customer_id = ? AND del_flg = 0', array($edit_customer_id));

            // 顧客情報を取得する事が出来たら、テンプレートに値を渡す
            if( 0 < count($arrCustomerInfo) && is_array($arrCustomerInfo) === true){
                // カラム名にorder_を付ける(テンプレート側でorder_がついている為
                foreach($arrCustomerInfo[0] as $index=>$customer_info){
                    // customer_idにはorder_を付けないようにする
                    $order_index = ($index == 'customer_id') ? $index : 'order_'.$index;
                    $arrCustomer[$order_index] = $customer_info;
                }
            }

            // hiddenに渡す
            $this->edit_customer_id = $edit_customer_id;

            // 受注日に現在の時刻を取得し、表示させる
            $create_date = $objQuery->getall('SELECT now() as create_date;');
            $arrCustomer['create_date'] = $create_date[0]['create_date'];

            // 情報上書き
            $this->objFormParam->setParam($arrCustomer);
            // 入力値の変換
            $this->objFormParam->convParam();
        }
    }
}
?>
