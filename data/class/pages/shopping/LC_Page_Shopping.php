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
 * ショッピングログインのページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Shopping extends LC_Page_Ex {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_title = 'ログイン';
        $masterData = new SC_DB_MasterData();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrSex = $masterData->getMasterData("mtb_sex");
        $this->arrJob = $masterData->getMasterData("mtb_job");
        $this->tpl_onload = 'fnCheckInputDeliv();';

        $objDate = new SC_Date(START_BIRTH_YEAR, date("Y",strtotime("now")));
        $this->arrYear = $objDate->getYear('', 1950, '');
        $this->arrMonth = $objDate->getMonth(true);
        $this->arrDay = $objDate->getDay(true);

        $this->httpCacheControl('nocache');
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
     * Page のプロセス.
     *
     * @return void
     */
    function action() {
        $objSiteSess = new SC_SiteSession();
        $objCartSess = new SC_CartSession();
        $objCustomer = new SC_Customer();
        $objCookie = new SC_Cookie();
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objFormParam = new SC_FormParam();

        $this->tpl_uniqid = $objSiteSess->getUniqId();
        $objPurchase->verifyChangeCart($this->tpl_uniqid, $objCartSess);

        $this->cartKey = $objCartSess->getKey();

        // ログイン済みの場合は次画面に遷移
        if ($objCustomer->isLoginSuccess(true)) {
            SC_Response_Ex::sendRedirect(
                    $this->getNextlocation($this->cartKey, $this->tpl_uniqid,
                                           $objCustomer, $objPurchase,
                                           $objSiteSess));
            exit;
        }
        // 非会員かつ, ダウンロード商品の場合はエラー表示
        else {
            if ($this->cartKey == PRODUCT_TYPE_DOWNLOAD) {
                SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, $objSiteSess, false,
                    "ダウンロード商品を含むお買い物は、会員登録が必要です。<br/>"
                  . "お手数ですが、会員登録をお願いします。");
                exit;
            }
        }

        switch ($this->getMode()) {
        // ログイン実行
        case 'login':
            $this->lfInitLoginFormParam($objFormParam);
            $objFormParam->setParam($_POST);
            $objFormParam->trimParam();
            $objFormParam->convParam();
            $objFormParam->toLower('login_email');
            $this->arrErr = $objFormParam->checkError();

            // ログイン判定
            if (SC_Utils_Ex::isBlank($this->arrErr)
                && $this->doLogin($objCustomer,
                                  $objFormParam->getValue('login_email'),
                                  $objFormParam->getValue('login_pass'))) {

                // モバイルサイトで携帯アドレスの登録が無い場合、携帯アドレス登録ページへ遷移
                if(SC_Display::detectDevice() == DEVICE_TYPE_MOBILE) {
                    if($this->hasEmailMobile($objCustomer) == false) {
                        SC_Response_Ex::sendRedirectFromUrlPath('entry/email_mobile.php');
                        exit;
                    }
                }
                
                SC_Response_Ex::sendRedirect(
                        $this->getNextLocation($this->cartKey, $this->tpl_uniqid,
                                               $objCustomer, $objPurchase,
                                               $objSiteSess));
                exit;
            }
            // ログインに失敗した場合
            else {
                // 仮登録の場合
                if($this->checkTempCustomer($objFormParam->getValue('login_email'))) {
                    SC_Utils_Ex::sfDispSiteError(TEMP_LOGIN_ERROR);
                    exit;
                } else {
                    SC_Utils_Ex::sfDispSiteError(SITE_LOGIN_ERROR);
                    exit;
                }
            }
            break;

        // お客様情報登録
        case 'nonmember_confirm':
            $this->tpl_mainpage = 'shopping/nonmember_input.tpl';
            $this->tpl_title = 'お客様情報入力';
            $this->lfInitParam($objFormParam);
            $objFormParam->setParam($_POST);

            $this->arrErr = $this->lfCheckError($objFormParam);

            if (SC_Utils_Ex::isBlank($this->arrErr)) {

                $this->lfRegistData($this->tpl_uniqid, $objPurchase,
                                    $objCustomer, $objFormParam);

                $objSiteSess->setRegistFlag();
                SC_Response_Ex::sendRedirect(SHOPPING_PAYMENT_URLPATH);
                exit;
            }
            break;

        // 前のページに戻る
        case 'return':
            SC_Response_Ex::sendRedirect(CART_URLPATH);
            exit;
            break;

        // 複数配送ページへ遷移
        case 'multiple':
            $this->lfInitParam($objFormParam);
            $objFormParam->setParam($_POST);
            $this->arrErr = $this->lfCheckError($objFormParam);

            if (SC_Utils_Ex::isBlank($this->arrErr)) {
                $this->lfRegistData($this->tpl_uniqid, $objPurchase,
                                    $objCustomer, $objFormParam, true);

                $objSiteSess->setRegistFlag();
                SC_Response_Ex::sendRedirect(MULTIPLE_URLPATH);
                exit;
            }
            // ※breakなし

        // お客様情報入力ページの表示
        case 'nonmember':
            $this->tpl_mainpage = 'shopping/nonmember_input.tpl';
            $this->tpl_title = 'お客様情報入力';
            // ※breakなし

        default:
            // 前のページから戻ってきた場合は, お客様情報入力ページ
            if (isset($_GET['from']) && $_GET['from'] == 'nonmember') {
                $this->tpl_mainpage = 'shopping/nonmember_input.tpl';
                $this->tpl_title = 'お客様情報入力';
                $this->lfInitParam($objFormParam);
            }
            // 通常はログインページ
            else {
                $this->lfInitLoginFormParam($objFormParam);
            }

            $this->setFormParams($objFormParam, $objPurchase, $this->tpl_uniqid);
            $objPurchase->unsetShippingTemp();
        }

        // 記憶したメールアドレスを取得
        $this->tpl_login_email = $objCookie->getCookie('login_email');
        if (!SC_Utils_Ex::isBlank($this->tpl_login_email)) {
            $this->tpl_login_memory = "1";
        }

        // 入力値の取得
        $this->arrForm = $objFormParam->getFormParamList();

        // 携帯端末IDが一致する会員が存在するかどうかをチェックする。
        if (SC_Display::detectDevice() === DEVICE_TYPE_MOBILE) {
            $this->tpl_valid_phone_id = $objCustomer->checkMobilePhoneId();
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

    /**
     * お客様情報入力時のパラメータ情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {

        $objFormParam->addParam("お名前(姓)", "order_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("お名前(名)", "order_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("お名前(フリガナ・姓)", "order_kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "KANA_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("お名前(フリガナ・名)", "order_kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "KANA_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("郵便番号1", "order_zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $objFormParam->addParam("郵便番号2", "order_zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $objFormParam->addParam("都道府県", "order_pref", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("住所1", "order_addr01", MTEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("住所2", "order_addr02", MTEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("電話番号1", "order_tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("電話番号2", "order_tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("電話番号3", "order_tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("FAX番号1", "order_fax01", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("FAX番号2", "order_fax02", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("FAX番号3", "order_fax03", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("メールアドレス", "order_email", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "NO_SPTAB", "MAX_LENGTH_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK"));
        $objFormParam->addParam("メールアドレス（確認）", "order_email02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "NO_SPTAB", "MAX_LENGTH_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK"), "", false);
        $objFormParam->addParam("年", "year", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
        $objFormParam->addParam("月", "month", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
        $objFormParam->addParam("日", "day", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
        $objFormParam->addParam("性別", "order_sex", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("職業", "order_job", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("別のお届け先", "deliv_check", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("お名前(姓)", "shipping_name01", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("お名前(名)", "shipping_name02", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("お名前(フリガナ・姓)", "shipping_kana01", STEXT_LEN, "KVCa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("お名前(フリガナ・名)", "shipping_kana02", STEXT_LEN, "KVCa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("郵便番号1", "shipping_zip01", ZIP01_LEN, "n", array("NUM_CHECK", "NUM_COUNT_CHECK"));
        $objFormParam->addParam("郵便番号2", "shipping_zip02", ZIP02_LEN, "n", array("NUM_CHECK", "NUM_COUNT_CHECK"));
        $objFormParam->addParam("都道府県", "shipping_pref", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("住所1", "shipping_addr01", MTEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("住所2", "shipping_addr02", MTEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("電話番号1", "shipping_tel01", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("電話番号2", "shipping_tel02", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("電話番号3", "shipping_tel03", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
        $objFormParam->addParam("メールマガジン", "mail_flag", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), 1);
    }

    /**
     * ログイン時のパラメータ情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitLoginFormParam(&$objFormParam) {
        $objFormParam->addParam("記憶する", "login_memory", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("メールアドレス", "login_email", STEXT_LEN, "KVa", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("パスワード", "login_pass", PASSWORD_LEN2, "", array("EXIST_CHECK"));
    }

    /**
     * ログイン済みの場合の遷移先を取得する.
     *
     * 商品種別IDが, ダウンロード商品の場合は, 会員情報を受注一時情報に保存し,
     * 支払方法選択画面のパスを返す.
     * それ以外は, お届け先選択画面のパスを返す.
     *
     * @param integer $product_type_id 商品種別ID
     * @param string $uniqid 受注一時テーブルのユニークID
     * @param SC_Customer $objCustomer SC_Customer インスタンス
     * @param SC_Helper_Purchase $objPurchase SC_Helper_Purchase インスタンス
     * @param SC_SiteSession $objSiteSess SC_SiteSession インスタンス
     * @return string 遷移先のパス
     */
    function getNextLocation($product_type_id, $uniqid, &$objCustomer, &$objPurchase, &$objSiteSess) {
        switch ($product_type_id) {
        case PRODUCT_TYPE_DOWNLOAD:
            $objPurchase->saveOrderTemp($uniqid, array(), $objCustomer);
            $objSiteSess->setRegistFlag();
            return 'payment.php';
            break;

        case PRODUCT_TYPE_NORMAL:
        default:
            return 'deliv.php';
        }
    }

    /**
     * データの一時登録を行う.
     *
     * @param integer $uniqid 受注一時テーブルのユニークID
     * @param SC_Helper_Purchase $objPurchase SC_Helper_Purchase インスタンス
     * @param SC_Customer $objCustomer SC_Customer インスタンス
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param boolean $isMultiple 複数配送の場合 true
     */
    function lfRegistData($uniqid, &$objPurchase, &$objCustomer, &$objFormParam,
                          $isMultiple = false) {
        $arrParams = $objFormParam->getHashArray();
        $arrValues = $objFormParam->getDbArray();
        // 登録データの作成
        $arrValues['order_birth'] = SC_Utils_Ex::sfGetTimestamp($arrParams['year'], $arrParams['month'], $arrParams['day']);
        $arrValues['update_date'] = 'Now()';
        $arrValues['customer_id'] = '0';

        // お届け先を指定しない場合、
        if ($arrParams['deliv_check'] != '1') {
            // order_* を shipping_* へコピー
            $objPurchase->copyFromOrder($arrValues, $arrParams);
        }

        /*
         * order_* と shipping_* をそれぞれ $_SESSION['shipping'][$shipping_id]
         * に, shipping_* というキーで保存
         */
        foreach ($arrValues as $key => $val) {
            if (preg_match('/^order_/', $key)) {
                $arrOrder['shipping_' . str_replace('order_', '', $key)] = $val;
            } elseif (preg_match('/^shipping_/', $key)) {
                $arrShipping[$key] = $val;
            }
        }

        if ($isMultiple) {
            $objPurchase->saveShippingTemp($arrOrder, 0);
            if ($arrParams['deliv_check'] == '1') {
                $objPurchase->saveShippingTemp($arrShipping, 1);
            }
        } else {
            if ($arrParams['deliv_check'] == '1') {
                $objPurchase->saveShippingTemp($arrShipping, 0);
            } else {
                $objPurchase->saveShippingTemp($arrOrder, 0);
            }
        }
        $objPurchase->saveOrderTemp($uniqid, $arrValues, $objCustomer);
    }

    /**
     * 入力内容のチェックを行う.
     *
     * 追加の必須チェック, 相関チェックを行うため, SC_CheckError を使用する.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return array エラー情報の配
     */
    function lfCheckError(&$objFormParam) {
        // 入力値の変換
        $objFormParam->convParam();
        $objFormParam->toLower('order_mail');
        $objFormParam->toLower('order_mail_check');

        $arrParams = $objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrParams);
        $objErr->arrErr = $objFormParam->checkError();

        // 別のお届け先チェック
        if (isset($arrParams['deliv_check']) && $arrParams['deliv_check'] == "1") {
            $objErr->doFunc(array("お名前(姓)", "shipping_name01"), array("EXIST_CHECK"));
            $objErr->doFunc(array("お名前(名)", "shipping_name02"), array("EXIST_CHECK"));
            $objErr->doFunc(array("お名前(フリガナ・姓)", "shipping_kana01"), array("EXIST_CHECK"));
            $objErr->doFunc(array("お名前(フリガナ・名)", "shipping_kana02"), array("EXIST_CHECK"));
            $objErr->doFunc(array("郵便番号1", "shipping_zip01"), array("EXIST_CHECK"));
            $objErr->doFunc(array("郵便番号2", "shipping_zip02"), array("EXIST_CHECK"));
            $objErr->doFunc(array("都道府県", "shipping_pref"), array("EXIST_CHECK"));
            $objErr->doFunc(array("住所1", "shipping_addr01"), array("EXIST_CHECK"));
            $objErr->doFunc(array("住所2", "shipping_addr02"), array("EXIST_CHECK"));
            $objErr->doFunc(array("電話番号1", "shipping_tel01"), array("EXIST_CHECK"));
            $objErr->doFunc(array("電話番号2", "shipping_tel02"), array("EXIST_CHECK"));
            $objErr->doFunc(array("電話番号3", "shipping_tel03"), array("EXIST_CHECK"));
        }

        // 複数項目チェック
        $objErr->doFunc(array("TEL", "order_tel01", "order_tel02", "order_tel03"), array("TEL_CHECK"));
        $objErr->doFunc(array("FAX", "order_fax01", "order_fax02", "order_fax03"), array("TEL_CHECK"));
        $objErr->doFunc(array("郵便番号", "order_zip01", "order_zip02"), array("ALL_EXIST_CHECK"));
        $objErr->doFunc(array("TEL", "shipping_tel01", "shipping_tel02", "shipping_tel03"), array("TEL_CHECK"));
        $objErr->doFunc(array("郵便番号", "shipping_zip01", "shipping_zip02"), array("ALL_EXIST_CHECK"));
        $objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_BIRTHDAY"));
        $objErr->doFunc(array("メールアドレス", "メールアドレス（確認）", "order_email", "order_email02"), array("EQUAL_CHECK"));

        return $objErr->arrErr;
    }

    /**
     * 入力済みの購入情報をフォームに設定する.
     *
     * 受注一時テーブル, セッションの配送情報から入力済みの購入情報を取得し,
     * フォームに設定する.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param SC_Helper_Purchase $objPurchase SC_Helper_Purchase インスタンス
     * @param integer $uniqid 購入一時情報のユニークID
     * @return void
     */
    function setFormParams(&$objFormParam, &$objPurchase, $uniqid) {
        $arrOrderTemp = $objPurchase->getOrderTemp($uniqid);
        if (SC_Utils_Ex::isBlank($arrOrderTemp)) {
            $arrOrderTemp = array('order_email' => "",
                                  'order_birth' => "");
        }
        $arrShippingTemp = $objPurchase->getShippingTemp();

        $objFormParam->setParam($arrOrderTemp);
        /*
         * count($arrShippingTemp) > 1 は複数配送であり,
         * $arrShippingTemp[0] は注文者が格納されている
         */
        if (count($arrShippingTemp) > 1) {
            $objFormParam->setParam($arrShippingTemp[1]);
        } else {
            $objFormParam->setParam($arrShippingTemp[0]);
        }
        $objFormParam->setValue('order_email02', $arrOrderTemp['order_email']);
        $objFormParam->setDBDate($arrOrderTemp['order_birth']);
    }

    /**
     * ログインを実行する.
     *
     * ログインを実行し, 成功した場合はユーザー情報をセッションに格納し,
     * true を返す.
     * モバイル端末の場合は, 携帯端末IDを保存する.
     * ログインに失敗した場合は, false を返す.
     *
     * @param SC_Customer $objCustomer SC_Customer インスタンス
     * @param string $login_email ログインメールアドレス
     * @param string $login_pass ログインパスワード
     * @return boolean ログインに成功した場合 true; 失敗した場合 false
     */
    function doLogin(&$objCustomer, $login_email, $login_pass) {
        switch (SC_Display::detectDevice()) {
        case DEVICE_TYPE_MOBILE:
            if(!$objCustomer->getCustomerDataFromMobilePhoneIdPass($login_pass) &&
               !$objCustomer->getCustomerDataFromEmailPass($login_pass, $login_email, true)) {
                return false;
            } else {
                $objCustomer->updateMobilePhoneId();
                return true;
            }
            break;

        case DEVICE_TYPE_SMARTPHONE:
        case DEVICE_TYPE_PC:
        default:
            if(!$objCustomer->getCustomerDataFromEmailPass($login_pass, $login_email)) {
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * ログインした会員の携帯メールアドレス登録があるかどうか
     *
     * ログインした会員の携帯メールアドレスの存在をチェックする
     *
     * @param SC_Customer $objCustomer SC_Customer インスタンス
     * @return boolean 会員の携帯メールアドレス登録がある場合 true
     */
    function hasEmailMobile(&$objCustomer) {
        $objMobile = new SC_Helper_Mobile_Ex();
        if (!$objMobile->gfIsMobileMailAddress($objCustomer->getValue('email'))) {
            if ($objCustomer->hasValue('email_mobile')) {
                return true;
            }
        }
        return false;
    }

    /**
     * 仮会員かどうかを判定する.
     *
     * @param string $login_email メールアドレス
     * @return boolean 仮会員の場合 true
     */
    function checkTempCustomer($login_email) {
        $objQuery =& SC_Query::getSingletonInstance();
        $where = "email = ? AND status = 1 AND del_flg = 0";
        $count = $objQuery->count("dtb_customer", $where, array($login_email));
        return $count > 0;
    }
}
?>
