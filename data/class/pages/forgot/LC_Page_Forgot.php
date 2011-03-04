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
 * パスワード発行 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Forgot extends LC_Page_Ex {

    // {{{ properties

    /** フォームパラメータの配列 */
    var $objFormParam;

    /** 秘密の質問の答え */
    var $arrReminder;

    /** 変更後パスワード */
    var $temp_password;

    /** エラーメッセージ */
    var $errmsg;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_title = "パスワードを忘れた方";
        $this->tpl_mainpage = 'forgot/index.tpl';
        $this->tpl_mainno = '';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrReminder = $masterData->getMasterData("mtb_reminder");
        $this->device_type = SC_Display_Ex::detectDevice();
        $this->httpCacheControl('nocache');
        // デフォルトログインアドレスロード
        $objCookie = new SC_Cookie_Ex(COOKIE_EXPIRE);
        $this->tpl_login_email = $objCookie->getCookie('login_email');        
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
        // パラメータ管理クラス
        $objFormParam = new SC_FormParam_Ex();

        switch($this->getMode()) {
            case 'mail_check':
                $this->lfInitMailCheckParam($objFormParam, $this->device_type);
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                $objFormParam->toLower('email');
                $this->arrForm = $objFormParam->getHashArray();
                $this->arrErr = $objFormParam->checkError();
                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    $this->errmsg = $this->lfCheckForgotMail($this->arrForm, $this->arrReminder);
                    if(SC_Utils_Ex::isBlank($this->errmsg)) {
                        $this->tpl_mainpage = 'forgot/secret.tpl';
                    }
                }
                break;
            case 'secret_check':
                $this->lfInitSecretCheckParam($objFormParam, $this->device_type);
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                $objFormParam->toLower('email');
                $this->arrForm = $objFormParam->getHashArray();
                $this->arrErr = $objFormParam->checkError();
                if (SC_Utils_Ex::isBlank($this->arrErr)) {
                    $this->errmsg = $this->lfCheckForgotSecret($this->arrForm, $this->arrReminder);
                    if(SC_Utils_Ex::isBlank($this->errmsg)) {
                        // 完了ページへ移動する
                        $this->tpl_mainpage = 'forgot/complete.tpl';
                        // transactionidを更新させたいので呼び出し元(ログインフォーム側)をリロード。
                        $this->tpl_onload .= 'opener.location.reload(true);';
                    }else{
                        // 秘密の答えが一致しなかった
                        $this->tpl_mainpage = 'forgot/secret.tpl';
                    }
                }else{
                    // 入力値エラー
                    $this->tpl_mainpage = 'forgot/secret.tpl';
                }
                break;
            default:
                break;
        }

        // ポップアップ用テンプレート設定
        if($this->device_type == DEVICE_TYPE_PC) {
            $this->setTemplate($this->tpl_mainpage);
        }
    }

    /**
     * メールアドレス・名前確認
     *
     * @param array $arrForm フォーム入力値
     * @param array $arrReminder リマインダー質問リスト
     * @return string エラー文字列 問題が無ければNULL
     */
    function lfCheckForgotMail(&$arrForm, &$arrReminder) {
        $errmsg = NULL;
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $where = "(email Like ? OR email_mobile Like ?) AND name01 Like ? AND name02 Like ? AND del_flg = 0";
        $arrVal = array($arrForm['email'], $arrForm['email'], $arrForm['name01'], $arrForm['name02']);
        $result = $objQuery->select("reminder, status", "dtb_customer", $where, $arrVal);
        if (isset($result[0]['reminder']) and isset($arrReminder[$result[0]['reminder']])) {
            // 会員状態の確認
            if($result[0]['status'] == '2') {
                // 正会員
                $arrForm['reminder'] = $result[0]['reminder'];
            } else if ($result[0]['status'] == '1') {
                // 仮会員
                $errmsg = 'ご入力のemailアドレスは現在仮登録中です。<br/>登録の際にお送りしたメールのURLにアクセスし、<br/>本会員登録をお願いします。';
            }
        } else {
            $errmsg = 'お名前に間違いがあるか、このメールアドレスは登録されていません。';
        }
        return $errmsg;
    }
    
    /**
     * メールアドレス確認におけるパラメーター情報の初期化
     *
     * @param array $objFormParam フォームパラメータークラス
     * @param array $device_type デバイスタイプ
     * @return void
     */
    function lfInitMailCheckParam(&$objFormParam, $device_type) {
        $objFormParam->addParam("お名前(姓)", 'name01', STEXT_LEN, "aKV", array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objFormParam->addParam("お名前(名)", 'name02', STEXT_LEN, "aKV", array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
        if ($device_type === DEVICE_TYPE_MOBILE){
            $objFormParam->addParam('メールアドレス', "email", MTEXT_LEN, "a", array("EXIST_CHECK", "EMAIL_CHECK", "NO_SPTAB" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK","MOBILE_EMAIL_CHECK"));
        } else {
            $objFormParam->addParam('メールアドレス', "email", MTEXT_LEN, "a", array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        }
        return;
    }

    /**
     * 秘密の質問確認
     *
     * @param array $arrForm フォーム入力値
     * @param array $arrReminder リマインダー質問リスト
     * @return string エラー文字列 問題が無ければNULL
     */
    function lfCheckForgotSecret(&$arrForm, &$arrReminder) {
        $errmsg = '';
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $cols = "customer_id, reminder, reminder_answer, salt";
        $table = "dtb_customer";
        $where = "(email Like ? OR email_mobile Like ?)"
                    . " AND name01 Like ? AND name02 Like ?"
                    . " AND status = 2 AND del_flg = 0";
        $arrVal = array($arrForm['email'], $arrForm['email'],
                            $arrForm['name01'], $arrForm['name02']);
        $result = $objQuery->select($cols, $table, $where, $arrVal);
        if (isset($result[0]['reminder']) and isset($arrReminder[$result[0]['reminder']])
                and $result[0]['reminder'] == $arrForm['reminder']) {
            
            if (SC_Utils_Ex::sfIsMatchHashPassword($arrForm['reminder_answer'],
                     $result[0]['reminder_answer'], $result[0]['salt'])) {
                // 秘密の答えが一致
                // 新しいパスワードを設定する
                $new_password = GC_Utils_Ex::gfMakePassword(8);
                if(FORGOT_MAIL == 1) {
                    // メールで変更通知をする
                    $objDb = new SC_Helper_DB_Ex();
                    $CONF = $objDb->sfGetBasisData();
                    $this->lfSendMail($CONF, $arrForm['email'], $arrForm['name01'], $new_password);
                }
                $sqlval = array();
                $sqlval['password'] = $new_password;
                SC_Helper_Customer_Ex::sfEditCustomerData($sqlval, $result[0]['customer_id']);
                $arrForm['new_password'] = $new_password;
            } else {
                // 秘密の答えが一致しなかった
                $errmsg = '秘密の質問が一致しませんでした。';
            }
        } else {
            //不正なアクセス リマインダー値が前画面と異なる。
            // 新リファクタリング基準ではここで遷移は不許可なのでエラー表示
            //SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, "", true);
            $errmsg = '秘密の質問が一致しませんでした。';
        }
        return $errmsg;
    }

    /**
     * 秘密の質問確認におけるパラメーター情報の初期化
     *
     * @param array $objFormParam フォームパラメータークラス
     * @param array $device_type デバイスタイプ
     * @return void
     */
    function lfInitSecretCheckParam(&$objFormParam, $device_type) {
        // メールチェックと同等のチェックを再度行う
        $this->lfInitMailCheckParam($objFormParam, $device_type);
        // 秘密の質問チェックの追加
        $objFormParam->addParam("パスワード確認用の質問", "reminder", STEXT_LEN, "n", array("EXIST_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("パスワード確認用の質問の答え", "reminder_answer", STEXT_LEN, "aKV", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        return;
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
     * パスワード変更お知らせメールを送信する.
     *
     * @param array $CONF 店舗基本情報の配列
     * @param string $email 送信先メールアドレス
     * @param string $customer_name 送信先氏名
     * @param string $new_password 変更後の新パスワード
     * @return void
     *
     * FIXME: メールテンプレート編集の方に足すのが望ましい
     */
    function lfSendMail(&$CONF, $email, $customer_name, $new_password){
        // パスワード変更お知らせメール送信
        $objMailText = new SC_SiteView_Ex(false);
        $objMailText->assign('customer_name', $customer_name);
        $objMailText->assign('new_password', $new_password);
        $toCustomerMail = $objMailText->fetch("mail_templates/forgot_mail.tpl");
        // メール送信オブジェクトによる送信処理
        $objMail = new SC_SendMail();
        $objMail->setItem(
            '' //宛先
            , $toCustomerMail //本文
            , $CONF['email03'] //配送元アドレス
            , $CONF['shop_name'] // 配送元名
            , $CONF['email03'] // reply to
            , $CONF['email04'] //return_path
            , $CONF['email04'] // errors_to
            );
        $objMail->setTo($email, $customer_name ." 様");
        $objMail->sendMail();
        return;
    }

}
?>
