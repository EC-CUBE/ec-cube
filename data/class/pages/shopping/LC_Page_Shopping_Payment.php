<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
require_once(CLASS_REALDIR . "pages/LC_Page.php");

/**
 * 支払い方法選択 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Shopping_Payment extends LC_Page {

    // {{{ properties

    /** フォームパラメータの配列 */
    var $objFormParam;

    /** 顧客情報のインスタンス */
    var $objCustomer;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_onload = "fnCheckInputPoint(); fnSetDelivTime('payment','payment_id','deliv_time_id');";
        $this->tpl_title = "お支払方法・お届け時間等の指定";
        $masterData = new SC_DB_MasterData();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $objSiteSess = new SC_SiteSession();
        $objCartSess = new SC_CartSession();
        $objPurchase = new SC_Helper_Purchase_Ex();
        $this->objCustomer = new SC_Customer();

        $this->shipping =& $objPurchase->getShippingTemp();
        $this->isMultiple = $objPurchase->isMultiple();

        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam();
        // POST値の取得
        $this->objFormParam->setParam($_POST);

        $uniqid = $objSiteSess->getUniqId();
        $objPurchase->verifyChangeCart($uniqid, $objCartSess);

        // ユニークIDを引き継ぐ
        $this->tpl_uniqid = $uniqid;

        $this->cartKey = $objCartSess->getKey();

        // 配送時間を取得
        $this->arrDelivTime = $objPurchase->getDelivTime($this->cartKey);

        // 会員ログインチェック
        if($this->objCustomer->isLoginSuccess()) {
            $this->tpl_login = '1';
            $this->tpl_user_point = $this->objCustomer->getValue('point');
            //戻り先URL
            if ($this->cartKey == PRODUCT_TYPE_DOWNLOAD) {
                // ダウンロード商品のみの場合はカート画面へ戻る
                $this->tpl_back_url = CART_URL_PATH;
            } else {
                $this->tpl_back_url = DELIV_URL_PATH;
            }
        } else {
            $this->tpl_back_url = SHOPPING_URL . "?from=nonmember";
        }

        // 一時受注テーブルの読込
        $arrOrderTemp = $objPurchase->getOrderTemp($uniqid);
        //不正遷移チェック（正常に受注情報が格納されていない場合は一旦カート画面まで戻す）
        if (!$arrOrderTemp) {
            SC_Response_Ex::sendRedirect(CART_URL_PATH);
            exit;
        }

        // カート内商品の集計処理を行う
        $this->cartItems = $objCartSess->getCartList($this->cartKey);
        $this->tpl_message = $objCartSess->checkProducts($this->cartKey);

        if (strlen($this->tpl_message) >= 1) {
            SC_Utils_Ex::sfDispSiteError(SOLD_OUT, '', true);
        }
        // FIXME 使用ポイント, 配送都道府県, 支払い方法, 手数料の扱い
        $this->arrData = $objCartSess->calculate($this->cartKey, $objCustomer);

        // 購入金額の取得
        $total_inctax = $objCartSess->getAllProductsTotal($this->cartKey);

        // 支払い方法の取得
        $this->arrPayment = $objPurchase->getPayment($total_inctax, $objCartSess->getAllProductClassID($this->cartKey));

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST['mode']) {
        case 'confirm':
            // 入力値の変換
            $this->objFormParam->convParam();
            $this->arrErr = $this->lfCheckError($this->arrData, $this->arrPayment);
            // 入力エラーなし
            if(count($this->arrErr) == 0) {

                foreach (array_keys($_SESSION['shipping']) as $key) {
                    $timeId = $this->objFormParam->getValue('deliv_time_id' . $key);
                    /* TODO
                     * SC_Purchase::getShippingTemp() で取得して,
                     * リファレンスで代入すると, セッションに添字を追加できない？
                     */
                    $_SESSION['shipping'][$key]['time_id'] = $timeId;
                    $_SESSION['shipping'][$key]['shipping_time'] = $this->arrDelivTime[$timeId];
                    $_SESSION['shipping'][$key]['shipping_date'] = $this->objFormParam->getValue('deliv_date' . $key);
                }
                $this->lfRegistData($uniqid, $objPurchase);

                // 正常に登録されたことを記録しておく
                $objSiteSess->setRegistFlag();
                // 確認ページへ移動
                SC_Response_Ex::sendRedirect(SHOPPING_CONFIRM_URL_PATH);
                exit;
            }else{
                // ユーザユニークIDの取得
                $uniqid = $objSiteSess->getUniqId();
                // 受注一時テーブルからの情報を格納
                $this->objFormParam->setParam($objPurchase->getOrderTemp($uniqid));
            }
            break;
        // 前のページに戻る
        case 'return':
            // 非会員の場合
            // 正常な推移であることを記録しておく
            $objSiteSess->setRegistFlag();
            SC_Response_Ex::sendRedirect(SHOPPING_URL);
            exit;
            break;

        default:
            // 受注一時テーブルからの情報を格納
            $this->objFormParam->setParam($arrOrderTemp);
            break;
        }

        // 支払い方法の画像があるなしを取得（$img_show true:ある false:なし）
        $this->img_show = $this->lfGetImgShow($this->arrPayment);
        // お届け日一覧の取得
        $this->arrDelivDate = $objPurchase->getDelivDate($objCartSess, $this->cartKey);
        $this->arrForm = $this->objFormParam->getFormParamList();
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
        $this->objFormParam->addParam("お支払い方法", "payment_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("ポイント", "use_point", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK", "ZERO_START"));
        $this->objFormParam->addParam("その他お問い合わせ", "message", LTEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("ポイントを使用する", "point_check", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), '2');

        for ($i = 0; $i < count($this->shipping); $i++) {
            $this->objFormParam->addParam("お届け時間", "deliv_time_id" . $i, INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
            $this->objFormParam->addParam("お届け日", "deliv_date" . $i, STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
        }
    }

  
    /* 入力内容のチェック */
    function lfCheckError($arrData, $arrPayment) {
        // 入力データを渡す。
        $arrRet =  $this->objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError();

        if (USE_POINT === false) {
            $_POST['point_check'] = "";
            $_POST['use_point'] = "0";
        }

        if (!isset($_POST['point_check'])) $_POST['point_check'] = "";

        if($_POST['point_check'] == '1') {
            $objErr->doFunc(array("ポイントを使用する", "point_check"), array("EXIST_CHECK"));
            $objErr->doFunc(array("ポイント", "use_point"), array("EXIST_CHECK"));
            $max_point = $this->objCustomer->getValue('point');
            if($max_point == "") {
                $max_point = 0;
            }
            // FIXME mobile 互換のため br は閉じない...
            if($arrRet['use_point'] > $max_point) {
                $objErr->arrErr['use_point'] = "※ ご利用ポイントが所持ポイントを超えています。<br>";
            }
            if(($arrRet['use_point'] * POINT_VALUE) > $arrData['subtotal']) {
                $objErr->arrErr['use_point'] = "※ ご利用ポイントがご購入金額を超えています。<br>";
            }
        }

        $objCartSess = new SC_CartSession();
        // 購入金額の取得得
        $total_inctax = $objCartSess->getAllProductsTotal($this->cartKey);
        $pay_flag = true;
        foreach ($arrPayment as $key => $payment) {
            if ($payment['payment_id'] == $arrRet['payment_id']) {
                $pay_flag = false;
                break;
            }
        }
        if ($pay_flag && $arrRet['payment_id'] != "") {
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }

        return $objErr->arrErr;
    }

    /* 支払い方法文字列の取得 */
    function lfGetPaymentInfo($payment_id) {
        $objQuery = new SC_Query();
        $where = "payment_id = ?";
        $arrRet = $objQuery->getRow("charge, payment_method", "dtb_payment", $where, array($payment_id));
        return (array($arrRet['charge'], $arrRet['payment_method']));
    }

    /* DBへデータの登録 */
    function lfRegistData($uniqid, &$objPurchase) {

        $sqlval = $this->objFormParam->getDbArray();
        // 登録データの作成
        $sqlval['order_temp_id'] = $uniqid;
        $sqlval['update_date'] = 'Now()';

        if (strlen($sqlval['payment_id']) >= 1) {
            list($sqlval['charge'], $sqlval['payment_method']) = $this->lfGetPaymentInfo($sqlval['payment_id']);
        }

        // 使用ポイントの設定
        if($sqlval['point_check'] != '1') {
            $sqlval['use_point'] = 0;
        }

        $objPurchase->saveOrderTemp($uniqid, $sqlval);
    }

    //一時受注テーブルからの情報を格納する
    function lfSetOrderTempData($uniqid, &$objPurchase) {
        $arrOrderTemp = $objPurchase->getOrderTemp($uniqid);
        $this->objFormParam->setParam($arrOrderTemp);
        return $this->objFormParam;
    }

    /* 支払い方法の画像があるなしを取得（$img_show true:ある false:なし） */
    function lfGetImgShow($arrPayment) {
        $img_show = false;
        foreach ($arrPayment as $payment) {
            if (strlen($payment["payment_image"]) > 0 ){
                $img_show = true;
                break;
            }
        }
        return $img_show;
    }
}
?>
