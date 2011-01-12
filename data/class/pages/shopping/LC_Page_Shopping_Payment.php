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

        // 戻るボタンの処理(モバイル)
        if (Net_UserAgent_Mobile::isMobile() === true) {
            if (!empty($_POST['return'])) {
                switch ($_POST['mode']) {
                case 'confirm':
                    $_POST['mode'] = 'payment';
                    break;
                default:
                    // 正常な推移であることを記録しておく
                    $objSiteSess->setRegistFlag();
                    if ($this->cartdown == 2) {
                        // ダウンロード商品のみの場合はカート画面へ戻る
                        $this->objDisplay->redirect($this->getLocation(CART_URL_PATH));
                    } else {
                        $this->objDisplay->redirect(SHOPPING_URL);
                    }
                    exit;
                }
            }
        }

        switch($_POST['mode']) {
        // お届け日時指定(モバイル)
        case 'deliv_date':
            // 入力値の変換
            $this->objFormParam->convParam();
            $this->arrErr = $this->lfCheckError($this->arrData, $this->arrPayment);
            if (!isset($this->arrErr['payment_id'])) {
                // 支払い方法の入力エラーなし
                $this->tpl_mainpage = 'shopping/deliv_date.tpl';
                $this->tpl_title = "お届け日時指定";
                break;
            } else {
                // ユーザユニークIDの取得
                $uniqid = $objSiteSess->getUniqId();
                // 受注一時テーブルからの情報を格納
                $this->lfSetOrderTempData($uniqid);
            }
            break;
        case 'confirm':
            // 入力値の変換
            $this->objFormParam->convParam();
            $this->arrErr = $this->lfCheckError($this->arrData, $this->arrPayment);
            // 入力エラーなし
            if(count($this->arrErr) == 0) {
                // DBへのデータ登録
                $this->lfRegistData($uniqid, $objPurchase);
                $_SESSION['shipping'][0]['time_id'] = $this->objFormParam->getValue('deliv_time_id');
                $_SESSION['shipping'][0]['deliv_date'] = $this->objFormParam->getValue('deliv_date');
                // 正常に登録されたことを記録しておく
                $objSiteSess->setRegistFlag();
                // 確認ページへ移動
                SC_Response_Ex::sendRedirect(SHOPPING_CONFIRM_URL_PATH);
                exit;
            }else{
                // ユーザユニークIDの取得
                $uniqid = $objSiteSess->getUniqId();
                // 受注一時テーブルからの情報を格納
                $this->lfSetOrderTempData($uniqid);
                if (Net_UserAgent_Mobile::isMobile() === true && !isset($this->arrErr['payment_id'])) {
                    // 支払い方法の入力エラーなし
                    $this->tpl_mainpage = 'shopping/deliv_date.tpl';
                    $this->tpl_title = "お届け日時指定";
                }
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

        // 配送時間を取得
        $this->arrDelivTime = $objPurchase->getDelivTime($this->cartKey);

        // 支払い方法の画像があるなしを取得（$img_show true:ある false:なし）
        $this->img_show = $this->lfGetImgShow($this->arrPayment);
        // FIXME お届け日一覧の取得
        $this->arrDelivDate = $this->lfGetDelivDate();

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
        $this->objFormParam->addParam("ご質問", "message", LTEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
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
        $arrRet = $objQuery->select("charge", "dtb_payment", $where, array($payment_id));
        return (array($arrRet[0]['charge'], $arrRet[0]['deliv_id']));
    }

    /* DBへデータの登録 */
    function lfRegistData($uniqid, &$objPurchase) {

        $sqlval = $this->objFormParam->getDbArray();
        // 登録データの作成
        $sqlval['order_temp_id'] = $uniqid;
        $sqlval['update_date'] = 'Now()';

        if (strlen($sqlval['payment_id']) >= 1) {
            // FIXME list($sqlval['charge'], $sqlval['deliv_id']) = $this->lfGetPaymentInfo($sqlval['payment_id']);
        }

        // 使用ポイントの設定
        if($sqlval['point_check'] != '1') {
            $sqlval['use_point'] = 0;
        }

        $objPurchase->saveOrderTemp($uniqid, $sqlval);
    }

    /* お届け日一覧を取得する */
    function lfGetDelivDate() {
        $objCartSess = new SC_CartSession();
        $objQuery = new SC_Query();
        // 商品IDの取得
        $max = $objCartSess->getMax();
        for($i = 1; $i <= $max; $i++) {
            if($_SESSION[$objCartSess->key][$i]['id'][0] != "") {
                $arrID['product_id'][$i] = $_SESSION[$objCartSess->key][$i]['id'][0];
            }
        }
        if(count($arrID['product_id']) > 0) {
            $id = implode(",", $arrID['product_id']);
            //商品から発送目安の取得
            $deliv_date = $objQuery->get("MAX(deliv_date_id)", "dtb_products", "product_id IN (".$id.")");
            //発送目安
            switch($deliv_date) {
            //即日発送
            case '1':
                $start_day = 1;
                break;
            //1-2日後
            case '2':
                $start_day = 3;
                break;
            //3-4日後
            case '3':
                $start_day = 5;
                break;
            //1週間以内
            case '4':
                $start_day = 8;
                break;
            //2週間以内
            case '5':
                $start_day = 15;
                break;
            //3週間以内
            case '6':
                $start_day = 22;
                break;
            //1ヶ月以内
            case '7':
                $start_day = 32;
                break;
            //2ヶ月以降
            case '8':
                $start_day = 62;
                break;
            //お取り寄せ(商品入荷後)
            case '9':
                $start_day = "";
                break;
            default:
                //お届け日が設定されていない場合
                $start_day = "";
                break;
            }
            //お届け可能日のスタート値から、お届け日の配列を取得する
            $arrDelivDate = $this->lfGetDateArray($start_day, DELIV_DATE_END_MAX);
        }
        return $arrDelivDate;
    }

    //お届け可能日のスタート値から、お届け日の配列を取得する
    function lfGetDateArray($start_day, $end_day) {
        $masterData = new SC_DB_MasterData();
        $arrWDAY = $masterData->getMasterData("mtb_wday");
        //お届け可能日のスタート値がセットされていれば
        if($start_day >= 1) {
            $now_time = time();
            $max_day = $start_day + $end_day;
            // 集計
            for ($i = $start_day; $i < $max_day; $i++) {
                // 基本時間から日数を追加していく
                $tmp_time = $now_time + ($i * 24 * 3600);
                list($y, $m, $d, $w) = split(" ", date("y m d w", $tmp_time));
                $val = sprintf("%02d/%02d/%02d(%s)", $y, $m, $d, $arrWDAY[$w]);
                $arrDate[$val] = $val;
            }
        } else {
            $arrDate = false;
        }
        return $arrDate;
    }

    //一時受注テーブルからの情報を格納する
    function lfSetOrderTempData($uniqid) {
        $objQuery = new SC_Query();
        $col = "payment_id, use_point, message, point_check ";
        $from = "dtb_order_temp";
        $where = "order_temp_id = ?";
        $arrRet = $objQuery->select($col, $from, $where, array($uniqid));
        // DB値の取得
        $this->objFormParam->setParam($arrRet[0]);
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

    /* 配送時間の配列を生成 */
    function lfSetDelivTime() {
        $objDb = new SC_Helper_DB_Ex();
        $objJson = new Services_JSON;

        // 配送時間の取得
        $arrRet = $objDb->sfGetDelivTime($this->cartKey);
        // JSONエンコード
        echo $objJson->encode($arrRet);
        exit;
    }
}
?>
