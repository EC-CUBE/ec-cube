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
 * 会員登録のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Entry.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_Entry extends LC_Page_Ex {

    // {{{ properties

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     * @return void
     */
    function init() {
        parent::init();
        $masterData         = new SC_DB_MasterData_Ex();
        $this->arrPref      = $masterData->getMasterData('mtb_pref');
        $this->arrJob       = $masterData->getMasterData('mtb_job');
        $this->arrReminder  = $masterData->getMasterData('mtb_reminder');

        // 生年月日選択肢の取得
        $objDate            = new SC_Date_Ex(BIRTH_YEAR, date('Y',strtotime('now')));
        $this->arrYear      = $objDate->getYear('', START_BIRTH_YEAR, '');
        $this->arrMonth     = $objDate->getMonth(true);
        $this->arrDay       = $objDate->getDay(true);

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
     * Page のプロセス
     * @return void
     */
    function action() {

        $objFormParam = new SC_FormParam_Ex();

        SC_Helper_Customer_Ex::sfCustomerEntryParam($objFormParam);
        $objFormParam->setParam($_POST);
        $arrForm  = $objFormParam->getHashArray();

        // PC時は規約ページからの遷移でなければエラー画面へ遷移する
        if ($this->lfCheckReferer($arrForm, $_SERVER['HTTP_REFERER']) === false) {
            SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, '', true);
        }

        // mobile用（戻るボタンでの遷移かどうかを判定）
        if (!empty($arrForm['return'])) {
            $_POST['mode'] = 'return';
        }

        switch ($this->getMode()) {
            case 'confirm':
                if (isset($_POST['submit_address'])) {
                    // 入力エラーチェック
                    $this->arrErr = $this->lfCheckError($_POST);
                    // 入力エラーの場合は終了
                    if (count($this->arrErr) == 0) {
                        // 郵便番号検索文作成
//                        $zipcode = $_POST['zip01'] . $_POST['zip02'];
                        $zipcode = $_POST['zipcode'];

                        // 郵便番号検索
                        $arrAdsList = SC_Utils_Ex::sfGetAddress($zipcode);

                        // 郵便番号が発見された場合
                        if (!empty($arrAdsList)) {
                            $data['pref'] = $arrAdsList[0]['state'];
                            $data['addr01'] = $arrAdsList[0]['city']. $arrAdsList[0]['town'];
                            $objFormParam->setParam($data);

                            // 該当無し
                        } else {
                            $this->arrErr['zipcode'] = t('c_* The corresponding address was not found.<br />_01');
                        }
                    }
                    $this->arrForm  = $objFormParam->getHashArray();
                    break;
                }

                //-- 確認
                $this->arrErr = SC_Helper_Customer_Ex::sfCustomerEntryErrorCheck($objFormParam);
                $this->arrForm  = $objFormParam->getHashArray();
                // 入力エラーなし
                if (empty($this->arrErr)) {
                    //パスワード表示
                    $this->passlen      = SC_Utils_Ex::sfPassLen(strlen($this->arrForm['password']));

                    $this->tpl_mainpage = 'entry/confirm.tpl';
                    $this->tpl_title    = t('c_Member registration_01');
                }
                break;
            case 'complete':
                //-- 会員登録と完了画面
                $this->arrErr = SC_Helper_Customer_Ex::sfCustomerEntryErrorCheck($objFormParam);
                $this->arrForm  = $objFormParam->getHashArray();
                if (empty($this->arrErr)) {

                    $uniqid             = $this->lfRegistCustomerData($this->lfMakeSqlVal($objFormParam));

                    $this->lfSendMail($uniqid, $this->arrForm);

                    // 仮会員が無効の場合
                    if (CUSTOMER_CONFIRM_MAIL == false) {
                        // ログイン状態にする
                        $objCustomer = new SC_Customer_Ex();
                        $objCustomer->setLogin($this->arrForm['email']);
                    }

                    // 完了ページに移動させる。
                    SC_Response_Ex::sendRedirect('complete.php', array('ci' => SC_Helper_Customer_Ex::sfGetCustomerId($uniqid)));
                }
                break;
            case 'return':
                $this->arrForm  = $objFormParam->getHashArray();
                break;
            default:
                break;
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

    // }}}
    // {{{ protected functions
    /**
     * 会員情報の登録
     *
     * @access private
     * @return uniqid
     */
    function lfRegistCustomerData($sqlval) {
        SC_Helper_Customer_Ex::sfEditCustomerData($sqlval);
        return $sqlval['secret_key'];
    }

    /**
     * 会員登録に必要なSQLパラメーターの配列を生成する.
     *
     * フォームに入力された情報を元に, SQLパラメーターの配列を生成する.
     * モバイル端末の場合は, email を email_mobile にコピーし,
     * mobile_phone_id に携帯端末IDを格納する.
     *
     * @param mixed $objFormParam
     * @access private
     * @return $arrResults
     */
    function lfMakeSqlVal(&$objFormParam) {
        $arrForm                = $objFormParam->getHashArray();
        $arrResults             = $objFormParam->getDbArray();

        // 生年月日の作成
        $arrResults['birth']    = SC_Utils_Ex::sfGetTimestamp($arrForm['year'], $arrForm['month'], $arrForm['day']);

        // 仮会員 1 本会員 2
        $arrResults['status']   = (CUSTOMER_CONFIRM_MAIL == true) ? '1' : '2';

        /*
         * secret_keyは、テーブルで重複許可されていない場合があるので、
         * 本会員登録では利用されないがセットしておく。
         */
        $arrResults['secret_key'] = SC_Helper_Customer_Ex::sfGetUniqSecretKey();

        // 入会時ポイント
        $CONF = SC_Helper_DB_Ex::sfGetBasisData();
        $arrResults['point'] = $CONF['welcome_point'];

        if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {
            // 携帯メールアドレス
            $arrResults['email_mobile']     = $arrResults['email'];
            // PHONE_IDを取り出す
            $arrResults['mobile_phone_id']  =  SC_MobileUserAgent_Ex::getId();
        }
        return $arrResults;
    }

    /**
     * 会員登録完了メール送信する
     *
     * @access private
     * @return void
     */
    function lfSendMail($uniqid, $arrForm) {
        $CONF           = SC_Helper_DB_Ex::sfGetBasisData();

        $objMailText    = new SC_SiteView_Ex();
        $objMailText->setPage($this);
        $objMailText->assign('CONF', $CONF);
        $objMailText->assign('name01', $arrForm['name01']);
        $objMailText->assign('name02', $arrForm['name02']);
        $objMailText->assign('uniqid', $uniqid);
        $objMailText->assignobj($this);

        $objHelperMail  = new SC_Helper_Mail_Ex();
        $objHelperMail->setPage($this);

        // 仮会員が有効の場合
        if (CUSTOMER_CONFIRM_MAIL == true) {
            $subject        = $objHelperMail->sfMakeSubject(t('c_Confirmation of member registration_02'));
            $toCustomerMail = $objMailText->fetch('mail_templates/customer_mail.tpl');
        } else {
            $subject        = $objHelperMail->sfMakeSubject(t('c_Completion of member registration_02'));
            $toCustomerMail = $objMailText->fetch('mail_templates/customer_regist_mail.tpl');
        }

        $objMail = new SC_SendMail_Ex();
        $objMail->setItem(
            ''                    // 宛先
            , $subject              // サブジェクト
            , $toCustomerMail       // 本文
            , $CONF['email03']      // 配送元アドレス
            , $CONF['shop_name']    // 配送元 名前
            , $CONF['email03']      // reply_to
            , $CONF['email04']      // return_path
            , $CONF['email04']      // Errors_to
            , $CONF['email01']      // Bcc
        );
        // 宛先の設定
        $objMail->setTo($arrForm['email'],
                        t('f_NAME_FULL_SIR_01', array('T_ARG1' => $arrForm['name01'], 'T_ARG2' => $arrForm['name02'])));

        $objMail->sendMail();
    }

    /**
     * kiyaku.php からの遷移の妥当性をチェックする
     *
     * 以下の内容をチェックし, 妥当であれば true を返す.
     * 1. 規約ページからの遷移かどうか
     * 2. PC及びスマートフォンかどうか
     * 3. $post に何も含まれていないかどうか
     *
     * @access protected
     * @param array $post $_POST のデータ
     * @param string $referer $_SERVER['HTTP_REFERER'] のデータ
     * @return boolean kiyaku.php からの妥当な遷移であれば true
     */
    function lfCheckReferer(&$post, $referer) {

        if (SC_Display_Ex::detectDevice() !== DEVICE_TYPE_MOBILE
            && empty($post)
            && (preg_match('/kiyaku.php/', basename($referer)) === 0)) {
            return false;
            }
        return true;
    }

    /**
     * 入力エラーのチェック.
     *
     * @param array $arrRequest リクエスト値($_GET)
     * @return array $arrErr エラーメッセージ配列
     */
    function lfCheckError($arrRequest) {
        // パラメーター管理クラス
        $objFormParam = new SC_FormParam_Ex();
        // パラメーター情報の初期化
//        $objFormParam->addParam(t('c_Postal code 1_01'), 'zip01', ZIP01_LEN, 'n', array('EXIST_CHECK', 'NUM_COUNT_CHECK', 'NUM_CHECK'));
//        $objFormParam->addParam(t('c_Postal code 2_01'), 'zip02', ZIP02_LEN, 'n', array('EXIST_CHECK', 'NUM_COUNT_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam(t('c_Postal code_01'), 'zipcode', ZIPCODE_LEN, 'n', array('EXIST_CHECK', 'MAX_LENGTH_CHECK', 'NUM_CHECK'));

        // リクエスト値をセット
//        $arrData['zip01'] = $arrRequest['zip01'];
//        $arrData['zip02'] = $arrRequest['zip02'];
        $arrData['zipcode'] = $arrRequest['zipcode'];
        $objFormParam->setParam($arrData);
        // エラーチェック
        $arrErr = $objFormParam->checkError();
        // 親ウィンドウの戻り値を格納するinputタグのnameのエラーチェック
        /*
        if (!$this->lfInputNameCheck($addData['zip01'])) {
            $arrErr['zip01'] = t('c_* Format is inadequate.<br />_01');
        }
        if (!$this->lfInputNameCheck($arrdata['zip02'])) {
            $arrErr['zip02'] = t('c_* Format is inadequate.<br />_01');
        }
        */
        if (!$this->lfInputNameCheck($arrData['zipcode'])) {
            $arrErr['zipcode'] = t('c_* Format is inadequate.<br />_01');
        }

        return $arrErr;
    }

    /**
     * エラーチェック.
     *
     * @param string $value
     * @return エラーなし：true エラー：false
     */
    function lfInputNameCheck($value) {
        // 半角英数字と_（アンダーバー）, []以外の文字を使用していたらエラー
        if (strlen($value) > 0 && !preg_match("/^[a-zA-Z0-9_\[\]]+$/", $value)) {
            return false;
        }

        return true;
    }
}
