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
require_once CLASS_EX_REALDIR . 'page_extends/LC_Page_Ex.php';

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
        $this->tpl_title = t('c_Login_01');
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrSex = $masterData->getMasterData('mtb_sex');
        $this->arrJob = $masterData->getMasterData('mtb_job');
        $this->tpl_onload = 'fnCheckInputDeliv();';

        $objDate = new SC_Date_Ex(BIRTH_YEAR, date('Y',strtotime('now')));
        $this->arrYear = $objDate->getYear('', START_BIRTH_YEAR, '');
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

        $objSiteSess = new SC_SiteSession_Ex();
        $objCartSess = new SC_CartSession_Ex();
        $objCustomer = new SC_Customer_Ex();
        $objCookie = new SC_Cookie_Ex();
        $objPurchase = new SC_Helper_Purchase_Ex();
        $objFormParam = new SC_FormParam_Ex();

        $nonmember_mainpage = 'shopping/nonmember_input.tpl';
        $nonmember_title = t('c_Enter customer information_01');

        $this->tpl_uniqid = $objSiteSess->getUniqId();
        $objPurchase->verifyChangeCart($this->tpl_uniqid, $objCartSess);

        $this->cartKey = $objCartSess->getKey();

        // ログイン済みの場合は次画面に遷移
        if ($objCustomer->isLoginSuccess(true)) {

            SC_Response_Ex::sendRedirect(
                    $this->getNextlocation($this->cartKey, $this->tpl_uniqid,
                                           $objCustomer, $objPurchase,
                                           $objSiteSess));
            SC_Response_Ex::actionExit();
        }
        // 非会員かつ, ダウンロード商品の場合はエラー表示
        else {
            if ($this->cartKey == PRODUCT_TYPE_DOWNLOAD) {
                $msg = t('c_For shopping that includes downloaded products, member registration is necessary.<br />Please carry out member registration._01');
                SC_Utils_Ex::sfDispSiteError(FREE_ERROR_MSG, $objSiteSess, false, $msg);
                SC_Response_Ex::actionExit();
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
                    && $objCustomer->doLogin($objFormParam->getValue('login_email'),
                                             $objFormParam->getValue('login_pass'))) {

                    // モバイルサイトで携帯アドレスの登録が無い場合、携帯アドレス登録ページへ遷移
                    if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {
                        if (!$objCustomer->hasValue('email_mobile')) {

                            SC_Response_Ex::sendRedirectFromUrlPath('entry/email_mobile.php');
                            SC_Response_Ex::actionExit();
                        }
                    }
                    // スマートフォンの場合はログイン成功を返す
                    elseif (SC_Display_Ex::detectDevice() === DEVICE_TYPE_SMARTPHONE) {

                        echo SC_Utils_Ex::jsonEncode(array('success' =>
                                                     $this->getNextLocation($this->cartKey, $this->tpl_uniqid,
                                                                            $objCustomer, $objPurchase,
                                                                            $objSiteSess)));
                        SC_Response_Ex::actionExit();
                    }

                    SC_Response_Ex::sendRedirect(
                            $this->getNextLocation($this->cartKey, $this->tpl_uniqid,
                                                   $objCustomer, $objPurchase,
                                                   $objSiteSess));
                    SC_Response_Ex::actionExit();
                }
                // ログインに失敗した場合
                else {
                    // 仮登録の場合
                    if (SC_Helper_Customer_Ex::checkTempCustomer($objFormParam->getValue('login_email'))) {
                        if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_SMARTPHONE) {
                            echo $this->lfGetErrorMessage(TEMP_LOGIN_ERROR);
                            SC_Response_Ex::actionExit();
                        } else {
                            SC_Utils_Ex::sfDispSiteError(TEMP_LOGIN_ERROR);
                            SC_Response_Ex::actionExit();
                        }
                    } else {
                        if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_SMARTPHONE) {
                            echo $this->lfGetErrorMessage(SITE_LOGIN_ERROR);
                            SC_Response_Ex::actionExit();
                        } else {
                            SC_Utils_Ex::sfDispSiteError(SITE_LOGIN_ERROR);
                            SC_Response_Ex::actionExit();
                        }
                    }
                }
                break;

            // お客様情報登録
            case 'nonmember_confirm':
                $this->tpl_mainpage = $nonmember_mainpage;
                $this->tpl_title = $nonmember_title;
                $this->lfInitParam($objFormParam);
                $objFormParam->setParam($_POST);
                $this->arrErr = $this->lfCheckError($objFormParam);

                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    $this->lfRegistData($this->tpl_uniqid, $objPurchase, $objCustomer, $objFormParam);

                    $arrParams = $objFormParam->getHashArray();
                    $shipping_id = $arrParams['deliv_check'] == '1' ? 1 : 0;
                    $objPurchase->setShipmentItemTempForSole($objCartSess, $shipping_id);

                    $objSiteSess->setRegistFlag();


                    SC_Response_Ex::sendRedirect(SHOPPING_PAYMENT_URLPATH);
                    SC_Response_Ex::actionExit();
                }
                break;

            // 前のページに戻る
            case 'return':

                SC_Response_Ex::sendRedirect(CART_URLPATH);
                SC_Response_Ex::actionExit();
                break;

            // 複数配送ページへ遷移
            case 'multiple':
                // 複数配送先指定が無効な場合はエラー
                if (USE_MULTIPLE_SHIPPING === false) {
                    SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, '', true);
                    SC_Response_Ex::actionExit();
                }

                $this->lfInitParam($objFormParam);
                $objFormParam->setParam($_POST);
                $this->arrErr = $this->lfCheckError($objFormParam);

                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    $this->lfRegistData($this->tpl_uniqid, $objPurchase, $objCustomer, $objFormParam, true);

                    $objSiteSess->setRegistFlag();


                    SC_Response_Ex::sendRedirect(MULTIPLE_URLPATH);
                    SC_Response_Ex::actionExit();
                }
                $this->tpl_mainpage = $nonmember_mainpage;
                $this->tpl_title = $nonmember_title;
                break;

            // お客様情報入力ページの表示
            case 'nonmember':
                $this->tpl_mainpage = $nonmember_mainpage;
                $this->tpl_title = $nonmember_title;
                $this->lfInitParam($objFormParam);
                // ※breakなし

            default:
                // 前のページから戻ってきた場合は, お客様情報入力ページ
                if (isset($_GET['from']) && $_GET['from'] == 'nonmember') {
                    $this->tpl_mainpage = $nonmember_mainpage;
                    $this->tpl_title = $nonmember_title;
                    $this->lfInitParam($objFormParam);
                }
                // 通常はログインページ
                else {
                    $this->lfInitLoginFormParam($objFormParam);
                }

                $this->setFormParams($objFormParam, $objPurchase, $this->tpl_uniqid);
                break;
        }

        // 記憶したメールアドレスを取得
        $this->tpl_login_email = $objCookie->getCookie('login_email');
        if (!SC_Utils_Ex::isBlank($this->tpl_login_email)) {
            $this->tpl_login_memory = '1';
        }

        // 入力値の取得
        $this->arrForm = $objFormParam->getFormParamList();

        // 携帯端末IDが一致する会員が存在するかどうかをチェックする。
        if (SC_Display_Ex::detectDevice() === DEVICE_TYPE_MOBILE) {
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
     * お客様情報入力時のパラメーター情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitParam(&$objFormParam) {

        $objFormParam->addParam(t('c_Name (last name)_01'), 'order_name01', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Name (first name)_01'), 'order_name02', STEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Name (last name) KANA_01'), 'order_kana01', STEXT_LEN, 'KVCa', array('KANA_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Name (first name) KANA_01'), 'order_kana02', STEXT_LEN, 'KVCa', array('KANA_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
//        $objFormParam->addParam(t('c_Postal code 1_01'), 'order_zip01', ZIP01_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'NUM_COUNT_CHECK'));
//        $objFormParam->addParam(t('c_Postal code 2_01'), 'order_zip02', ZIP02_LEN, 'n', array('EXIST_CHECK', 'NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam(t('c_Postal code_01'), 'order_zipcode',INT_LEN, 'n', array('NUM_CHECK'));
        $objFormParam->addParam(t('c_Prefecture_01'), 'order_pref', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('c_Address 1_01'), 'order_addr01', MTEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Address 2_01'), 'order_addr02', MTEXT_LEN, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Telephone number 1_01'), 'order_tel01', TEL_ITEM_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Telephone number 2_01'), 'order_tel02', TEL_ITEM_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Telephone number 3_01'), 'order_tel03', TEL_ITEM_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Fax number 1_01'), 'order_fax01', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Fax number 2_01'), 'order_fax02', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Fax number 3_01'), 'order_fax03', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_E-mail address_01'), 'order_email', null, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'NO_SPTAB', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'));
        $objFormParam->addParam(t('c_E-mail address (confirmation)_01'), 'order_email02', null, 'KVa', array('EXIST_CHECK', 'SPTAB_CHECK', 'NO_SPTAB', 'EMAIL_CHECK', 'EMAIL_CHAR_CHECK'), '', false);
        $objFormParam->addParam(t('c_Year_01'), 'year', INT_LEN, 'n', array('MAX_LENGTH_CHECK'), '', false);
        $objFormParam->addParam(t('c_Month_01'), 'month', INT_LEN, 'n', array('MAX_LENGTH_CHECK'), '', false);
        $objFormParam->addParam(t('c_Day_01'), 'day', INT_LEN, 'n', array('MAX_LENGTH_CHECK'), '', false);
        $objFormParam->addParam(t('c_Gender_01'), 'order_sex', INT_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('c_Occupation_01'), 'order_job', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('c_Separate shipping destination_01'), 'deliv_check', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('c_Name (last name)_01'), 'shipping_name01', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Name (first name)_01'), 'shipping_name02', STEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Name (last name) KANA_01'), 'shipping_kana01', STEXT_LEN, 'KVCa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Name (first name) KANA_01'), 'shipping_kana02', STEXT_LEN, 'KVCa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
//        $objFormParam->addParam(t('c_Postal code 1_01'), 'shipping_zip01', ZIP01_LEN, 'n', array('NUM_CHECK', 'NUM_COUNT_CHECK'));
//        $objFormParam->addParam(t('c_Postal code 2_01'), 'shipping_zip02', ZIP02_LEN, 'n', array('NUM_CHECK', 'NUM_COUNT_CHECK'));
        $objFormParam->addParam(t('c_Postal code_01'), 'shipping_zipcode',  INT_LEN, 'n', array('NUM_CHECK'));
        $objFormParam->addParam(t('c_Prefecture_01'), 'shipping_pref', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('c_Address 1_01'), 'shipping_addr01', MTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Address 2_01'), 'shipping_addr02', MTEXT_LEN, 'KVa', array('SPTAB_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Telephone number 1_01'), 'shipping_tel01', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Telephone number 2_01'), 'shipping_tel02', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Telephone number 3_01'), 'shipping_tel03', TEL_ITEM_LEN, 'n', array('MAX_LENGTH_CHECK' ,'NUM_CHECK'));
        $objFormParam->addParam(t('c_Mail magazine_01'), 'mail_flag', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'), 1);
    }

    /**
     * ログイン時のパラメーター情報の初期化を行う.
     *
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @return void
     */
    function lfInitLoginFormParam(&$objFormParam) {
        $objFormParam->addParam(t('c_Record_01'), 'login_memory', INT_LEN, 'n', array('MAX_LENGTH_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('c_E-mail address_01'), 'login_email', STEXT_LEN, 'a', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
        $objFormParam->addParam(t('c_Password_01'), 'login_pass', PASSWORD_MAX_LEN, '', array('EXIST_CHECK', 'MAX_LENGTH_CHECK'));
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

            case PRODUCT_TYPE_NORMAL:
            default:
                return 'deliv.php';
        }
    }

    /**
     * データの一時登録を行う.
     *
     * 非会員向けの処理
     * @param integer $uniqid 受注一時テーブルのユニークID
     * @param SC_Helper_Purchase $objPurchase SC_Helper_Purchase インスタンス
     * @param SC_Customer $objCustomer SC_Customer インスタンス
     * @param SC_FormParam $objFormParam SC_FormParam インスタンス
     * @param boolean $isMultiple 複数配送の場合 true
     */
    function lfRegistData($uniqid, &$objPurchase, &$objCustomer, &$objFormParam, $isMultiple = false) {
        $arrParams = $objFormParam->getHashArray();

        // 注文者をお届け先とする配列を取得
        $arrShippingOwn = array();
        $objPurchase->copyFromOrder($arrShippingOwn, $arrParams);

        // 都度入力されたお届け先
        $arrShipping = $objPurchase->extractShipping($arrParams);

        if ($isMultiple) {
            $objPurchase->unsetOneShippingTemp(0);
            $objPurchase->unsetOneShippingTemp(1);
            $objPurchase->saveShippingTemp($arrShippingOwn, 0);
            if ($arrParams['deliv_check'] == '1') {
                $objPurchase->saveShippingTemp($arrShipping, 1);
            }
        } else {
            $objPurchase->unsetAllShippingTemp(true);
            if ($arrParams['deliv_check'] == '1') {
                $objPurchase->saveShippingTemp($arrShipping, 1);
            } else {
                $objPurchase->saveShippingTemp($arrShippingOwn, 0);
            }
        }

        $arrValues = $objFormParam->getDbArray();

        // 登録データの作成
        $arrValues['order_birth'] = SC_Utils_Ex::sfGetTimestamp($arrParams['year'], $arrParams['month'], $arrParams['day']);
        $arrValues['update_date'] = 'CURRENT_TIMESTAMP';
        $arrValues['customer_id'] = '0';
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
        $objErr = new SC_CheckError_Ex($arrParams);
        $objErr->arrErr = $objFormParam->checkError();

        // 別のお届け先チェック
        if (isset($arrParams['deliv_check']) && $arrParams['deliv_check'] == '1') {
            $objErr->doFunc(array(t('c_Name (last name)_01'), 'shipping_name01'), array('EXIST_CHECK'));
            $objErr->doFunc(array(t('c_Name (first name)_01'), 'shipping_name02'), array('EXIST_CHECK'));
//            $objErr->doFunc(array(t('c_Name (last name) KANA_01'), 'shipping_kana01'), array('EXIST_CHECK'));
//            $objErr->doFunc(array(t('c_Name (first name) KANA_01'), 'shipping_kana02'), array('EXIST_CHECK'));
//            $objErr->doFunc(array(t('c_Postal code 1_01'), 'shipping_zip01'), array('EXIST_CHECK'));
//            $objErr->doFunc(array(t('c_Postal code 2_01'), 'shipping_zip02'), array('EXIST_CHECK'));
            $objErr->doFunc(array(t('c_Postal code_01'), 'shipping_zipcode'), array('EXIST_CHECK'));
            $objErr->doFunc(array(t('c_Address 1_01'), 'shipping_addr01'), array('EXIST_CHECK'));
            $objErr->doFunc(array(t('c_Address 2_01'), 'shipping_addr02'), array('EXIST_CHECK'));
            $objErr->doFunc(array(t('c_Telephone number 1_01'), 'shipping_tel01'), array('EXIST_CHECK'));
            $objErr->doFunc(array(t('c_Telephone number 2_01'), 'shipping_tel02'), array('EXIST_CHECK'));
            $objErr->doFunc(array(t('c_Telephone number 3_01'), 'shipping_tel03'), array('EXIST_CHECK'));
        }

        // 複数項目チェック
        $objErr->doFunc(array(t('c_TEL_01'), 'order_tel01', 'order_tel02', 'order_tel03'), array('TEL_CHECK'));
        $objErr->doFunc(array(t('c_FAX_01'), 'order_fax01', 'order_fax02', 'order_fax03'), array('TEL_CHECK'));
//        $objErr->doFunc(array(t('c_Postal code_01'), 'order_zip01', 'order_zip02'), array('ALL_EXIST_CHECK'));
        $objErr->doFunc(array(t('c_TEL_01'), 'shipping_tel01', 'shipping_tel02', 'shipping_tel03'), array('TEL_CHECK'));
//        $objErr->doFunc(array(t('c_Postal code_01'), 'shipping_zip01', 'shipping_zip02'), array('ALL_EXIST_CHECK'));
        $objErr->doFunc(array(t('c_Date of birth_01'), 'year', 'month', 'day'), array('CHECK_BIRTHDAY'));
        $objErr->doFunc(array(t('c_E-mail address_01'), t('c_E-mail address (confirmation)_01'), 'order_email', 'order_email02'), array('EQUAL_CHECK'));

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
            $arrOrderTemp = array(
                'order_email' => '',
                'order_birth' => '',
            );
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
     * エラーメッセージを JSON 形式で返す.
     *
     * TODO リファクタリング
     * この関数は主にスマートフォンで使用します.
     *
     * @param integer エラーコード
     * @return string JSON 形式のエラーメッセージ
     * @see LC_PageError
     */
    function lfGetErrorMessage($error) {
        switch ($error) {
            case TEMP_LOGIN_ERROR:
                $msg = t('c_The e-mail address or password is not correct.If you have not completed registration, complete registration from the URL given in the temporary registration e-mail._01');
                break;
            case SITE_LOGIN_ERROR:
            default:
                $msg = t('c_The e-mail address or password is not correct._01');
        }
        return SC_Utils_Ex::jsonEncode(array('login_error' => $msg));
    }
}
