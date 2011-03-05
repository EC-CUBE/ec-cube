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
require_once(CLASS_EX_REALDIR . "page_extends/LC_Page_Ex.php");

/**
 * 支払い方法選択 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Shopping_Payment extends LC_Page_Ex {

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
        $this->tpl_onload = "fnCheckInputPoint();";
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
        $objSiteSess = new SC_SiteSession_Ex();
        $objCartSess = new SC_CartSession_Ex();
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objCustomer = new SC_Customer_Ex();
        $objFormParam = new SC_FormParam_Ex();

        // カートの情報を取得
        $this->arrShipping =& $objPurchase->getShippingTemp();
        $shipping_vol = count($this->arrShipping);

        $this->is_multiple = $objPurchase->isMultiple();
        $this->tpl_uniqid = $objSiteSess->getUniqId();
        $cart_key = $objCartSess->getKey();
        $objPurchase->verifyChangeCart($this->tpl_uniqid, $objCartSess);

        // 配送業者を取得
        $this->arrDeliv = $objPurchase->getDeliv($cart_key);
        $this->is_single_deliv = $this->isSingleDeliv($this->arrDeliv);

        // 会員情報の取得
        if ($objCustomer->isLoginSuccess(true)) {
            $this->tpl_login = '1';
            $this->tpl_user_point = $objCustomer->getValue('point');
            $this->name01 = $objCustomer->getValue('name01');
            $this->name02 = $objCustomer->getValue('name02');
        }

        // 戻り URL の設定
        $this->tpl_back_url = $this->getPreviousURL($objCustomer->isLoginSuccess(true), $cart_key);

        $arrOrderTemp = $objPurchase->getOrderTemp($this->tpl_uniqid);
        // 正常に受注情報が格納されていない場合はカート画面へ戻す
        if (SC_Utils_Ex::isBlank($arrOrderTemp)) {
            SC_Response_Ex::sendRedirect(CART_URLPATH);
            exit;
        }

        // カート内商品の妥当性チェック
        $this->tpl_message = $objCartSess->checkProducts($cart_key);
        if (strlen($this->tpl_message) >= 1) {
            SC_Response_Ex::sendRedirect(CART_URLPATH);
            exit;
        }

        // 購入金額の取得
        $this->arrPrices = $objCartSess->calculate($cart_key, $objCustomer, 0, $objPurchase->getShippingPref());

        // お届け日一覧の取得
        $this->arrDelivDate = $objPurchase->getDelivDate($objCartSess, $cart_key);

        switch($this->getMode()) {
        /*
         * 配送業者選択時のアクション
         * モバイル端末以外の場合は, JSON 形式のデータを出力し, ajax で取得する.
         */
        case 'select_deliv':
            $this->setFormParams($objFormParam, $_POST, true, $shipping_vol);

            $arrErr = $objFormParam->checkError();
            if (SC_Utils_Ex::isBlank($arrErr)) {
                $deliv_id = $objFormParam->getValue('deliv_id');
                $arrSelectedDeliv = $this->getSelectedDeliv($objPurchase, $objCartSess, $deliv_id);
                $arrSelectedDeliv['error'] = false;
            } else {
                $arrSelectedDeliv = array('error' => true);
            }

            if (SC_Display_Ex::detectDevice() != DEVICE_TYPE_MOBILE) {
                echo SC_Utils_Ex::jsonEncode($arrSelectedDeliv);
                exit;
            } else {
                $this->arrPayment = $arrSelectedDeliv['arrPayment'];
                $this->arrDelivTime = $arrSelectedDeliv['arrDelivTime'];
            }
            break;

        // 登録処理
        case 'confirm':
            // パラメータ情報の初期化
            $this->setFormParams($objFormParam, $_POST, false, $shipping_vol);

            $deliv_id = $objFormParam->getValue('deliv_id');
            $arrSelectedDeliv = $this->getSelectedDeliv($objPurchase, $objCartSess, $deliv_id);
            $this->arrPayment = $arrSelectedDeliv['arrPayment'];
            $this->arrDelivTime = $arrSelectedDeliv['arrDelivTime'];

            $this->arrErr = $this->lfCheckError($objFormParam, $this->arrPrices['subtotal'], $this->tpl_user_point);

            if (SC_Utils_Ex::isBlank($this->arrErr)) {
                $this->saveShippings($objFormParam, $this->arrDelivTime);
                $this->lfRegistData($this->tpl_uniqid, $objFormParam->getDbArray(), $objPurchase, $this->arrPayment);

                // 正常に登録されたことを記録しておく
                $objSiteSess->setRegistFlag();
                // 確認ページへ移動
                SC_Response_Ex::sendRedirect(SHOPPING_CONFIRM_URLPATH);
                exit;
            } else {
                // 受注一時テーブルからの情報を格納
                $this->img_show = $arrSelectedDeliv['img_show'];
                $objFormParam->setParam($objPurchase->getOrderTemp($this->tpl_uniqid));
            }
            break;

        // 前のページに戻る
        case 'return':

            // 正常な推移であることを記録しておく
            $objSiteSess->setRegistFlag();
            SC_Response_Ex::sendRedirect(SHOPPING_URL);
            exit;
            break;

        default:
            // FIXME 前のページから戻ってきた場合は別パラメータ(mode)で処理分岐する必要があるのかもしれない
            $this->setFormParams($objFormParam, $arrOrderTemp, false, $shipping_vol);

            if (!$this->is_single_deliv) {
                $deliv_id = $objFormParam->getValue('deliv_id');
            } else {
                $deliv_id = $this->arrDeliv[0]['deliv_id'];
            }

            if (!SC_Utils_Ex::isBlank($deliv_id)) {
                $objFormParam->setValue('deliv_id', $deliv_id);
                $arrSelectedDeliv = $this->getSelectedDeliv($objPurchase, $objCartSess, $deliv_id);
                $this->arrPayment = $arrSelectedDeliv['arrPayment'];
                // XXX セッションからデフォルト値を取得する必要あり
                $this->arrDelivTime = $arrSelectedDeliv['arrDelivTime'];
                $this->img_show = $arrSelectedDeliv['img_show'];
            }
            break;
        }

        // モバイル用 ポストバック処理
        if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {
            $this->tpl_mainpage = $this->getMobileMainpage($this->is_single_deliv, $this->getMode());
        }

        $this->arrForm = $objFormParam->getFormParamList();
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
     * パラメータの初期化を行い, 初期値を設定する.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param array $arrParam 設定する値の配列
     * @param boolean $deliv_only deliv_id チェックのみの場合 true
     * @param integer $shipping_vol 配送数
     */
    function setFormParams(&$objFormParam, $arrParam, $deliv_only, $shipping_vol) {
        $this->lfInitParam($objFormParam, $deliv_only, $shipping_vol);
        $objFormParam->setParam($arrParam);
        $objFormParam->convParam();
    }

    /**
     * パラメータ情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param boolean $deliv_only deliv_id チェックのみの場合 true
     * @param integer $shipping_vol 配送数
     * @return void
     */
    function lfInitParam(&$objFormParam, $deliv_only, $shipping_vol) {
        $objFormParam->addParam("配送業者", "deliv_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));

        if (!$deliv_only) {
            $objFormParam->addParam("お支払い方法", "payment_id", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
            $objFormParam->addParam("ポイント", "use_point", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK", "ZERO_START"));
            $objFormParam->addParam("その他お問い合わせ", "message", LTEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
            $objFormParam->addParam("ポイントを使用する", "point_check", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), '2');

            for ($i = 0; $i < $shipping_vol; $i++) {
                $objFormParam->addParam("お届け時間", "deliv_time_id" . $i, INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
                $objFormParam->addParam("お届け日", "deliv_date" . $i, STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
            }
        }

        $objFormParam->setParam($arrParam);
        $objFormParam->convParam();
    }

    /**
     * 入力内容のチェックを行なう.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param integer $subtotal 購入金額の小計
     * @param integer $max_point 会員の保持ポイント
     * @return array 入力チェック結果の配列
     */
    function lfCheckError(&$objFormParam, $subtotal, $max_point) {
        // 入力データを渡す。
        $arrForm =  $objFormParam->getHashArray();
        $objErr = new SC_CheckError_Ex($arrForm);
        $objErr->arrErr = $objFormParam->checkError();

        if (USE_POINT === false) {
            return $objErr->arrErr;
        }

        if($arrForm['point_check'] == '1') {
            $objErr->doFunc(array("ポイントを使用する", "point_check"), array("EXIST_CHECK"));
            $objErr->doFunc(array("ポイント", "use_point"), array("EXIST_CHECK"));
            if($max_point == "") {
                $max_point = 0;
            }
            // FIXME mobile 互換のため br は閉じない...
            if($arrForm['use_point'] > $max_point) {
                $objErr->arrErr['use_point'] = "※ ご利用ポイントが所持ポイントを超えています。<br>";
            }
            if(($arrForm['use_point'] * POINT_VALUE) > $subtotal) {
                $objErr->arrErr['use_point'] = "※ ご利用ポイントがご購入金額を超えています。<br>";
            }
        }
        return $objErr->arrErr;
    }

    /**
     * 配送情報を保存する.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param array $arrDelivTime 配送時間の配列
     */
    function saveShippings(&$objFormParam, $arrDelivTime) {
        $deliv_id = $objFormParam->getValue('deliv_id');

        /* TODO
         * SC_Purchase::getShippingTemp() で取得して,
         * リファレンスで代入すると, セッションに添字を追加できない？
         */
        foreach (array_keys($_SESSION['shipping']) as $key) {

            $time_id = $objFormParam->getValue('deliv_time_id' . $key);

            $_SESSION['shipping'][$key]['deliv_id'] = $deliv_id;
            $_SESSION['shipping'][$key]['time_id'] = $time_id;
            $_SESSION['shipping'][$key]['shipping_time'] = $arrDelivTime[$time_id];
            $_SESSION['shipping'][$key]['shipping_date'] = $objFormParam->getValue('deliv_date' . $key);
        }
    }

    /**
     * 受注一時テーブルへ登録を行う.
     *
     * @param integer $uniqid 受注一時テーブルのユニークID
     * @param array $arrForm フォームの入力値
     * @param SC_Helper_Purchase $objPurchase SC_Helper_Purchase インスタンス
     * @param array $arrPayment お支払い方法の配列
     * @return void
     */
    function lfRegistData($uniqid, $arrForm, &$objPurchase, $arrPayment) {

        $arrForm['order_temp_id'] = $uniqid;
        $arrForm['update_date'] = 'Now()';

        if($arrForm['point_check'] != '1') {
            $arrForm['use_point'] = 0;
        }

        foreach ($arrPayment as $payment) {
            if ($arrForm['payment_id'] == $payment['payment_id']) {
                $arrForm['charge'] = $payment['charge'];
                $arrForm['payment_method'] = $payment['payment_method'];
                break;
            }
        }
        $objPurchase->saveOrderTemp($uniqid, $arrForm);
    }

    /**
     * 配送業者IDから, 支払い方法, お届け時間の配列を取得する.
     *
     * 結果の連想配列の添字の値は以下の通り
     * - 'arrDelivTime' - お届け時間の配列
     * - 'arrPayment' - 支払い方法の配列
     * - 'img_show' - 支払い方法の画像の有無
     *
     * @param SC_Helper_Purchase $objPurchase SC_Helper_Purchase インスタンス
     * @param SC_CartSession $objCartSess SC_CartSession インスタンス
     * @param integer $deliv_id 配送業者ID
     * @return array 支払い方法, お届け時間を格納した配列
     */
    function getSelectedDeliv(&$objPurchase, &$objCartSess, $deliv_id) {
        $arrResults = array();
        $arrResults['arrDelivTime'] = $objPurchase->getDelivTime($deliv_id);
        $total = $objCartSess->getAllProductsTotal($objCartSess->getKey(),
                                                   $deliv_id);
        $arrResults['arrPayment'] = $objPurchase->getPaymentsByPrice($total,
                                                                     $deliv_id);
        $arrResults['img_show'] = $this->hasPaymentImage($arrResults['arrPayment']);
        return $arrResults;
    }

    /**
     * 支払い方法の画像があるかどうか.
     *
     * @param array $arrPayment 支払い方法の配列
     * @return boolean 支払い方法の画像がある場合 true
     */
    function hasPaymentImage($arrPayment) {
        foreach ($arrPayment as $val) {
            if (!SC_Utils_Ex::isBlank($val['payment_image'])) {
                return true;
            }
        }
        return false;
    }

    /**
     * 配送業者が1社のみかどうか.
     *
     * @param array $arrDeliv 配送業者の配列
     * @return boolean 配送業者が1社のみの場合 true
     */
    function isSingleDeliv($arrDeliv) {
        if (count($arrDeliv) == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 前に戻るボタンの URL を取得する.
     *
     * @param boolean $is_login ユーザーがログインしている場合 true
     * @param $product_type_id 商品種別ID
     * @return string 前に戻るボタンの URL
     */
    function getPreviousURL($is_login = false, $product_type_id) {
        if ($is_login) {
            if ($product_type_id == PRODUCT_TYPE_DOWNLOAD) {
                return CART_URLPATH;
            } else {
                return DELIV_URLPATH;
            }
        } else {
            return SHOPPING_URL . "?from=nonmember";
        }
    }

    /**
     * モバイル用テンプレートのパスを取得する.
     *
     * @param boolean $is_single_deliv 配送業者が1社の場合 true
     * @param string $mode フォームパラメータ "mode" の文字列
     * @return string モバイル用テンプレートのパス
     */
    function getMobileMainpage($is_single_deliv = true, $mode) {
        switch($mode) {
        case 'select_deliv':
            return 'shopping/payment.tpl';
            break;

        case 'confirm':
        case 'return':
        default:
            if ($is_single_deliv) {
                return 'shopping/payment.tpl';
            } else {
                return 'shopping/select_deliv.tpl';
            }
        }
    }
}
?>
