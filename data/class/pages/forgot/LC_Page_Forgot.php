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
 * パスワード発行 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Forgot extends LC_Page {

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
        $this->isMobile = Net_UserAgent_Mobile::isMobile();
        $this->httpCacheControl('nocache');
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
        $objQuery = new SC_Query();
        
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!SC_Helper_Session_Ex::isValidToken()) {
                SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, "", true);
            }
        }
        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        switch($_POST["mode"]) {
            case 'mail_check':
                $this->lfForgotMailCheck();
                break;
            case 'secret_check':
                $this->lfForgotSecretCheck();
                break;
            default:
                $this->lfForgotDefault();
                break;
        }

        $this->transactionid = SC_Helper_Session_Ex::getToken();

        if ($this->isMobile) {
            // モバイルサイトの場合はトークン生成
            $this->createMobileToken();
        } else {
            // モバイルサイト以外の場合、ポップアップ用テンプレートファイル設定
            $this->setTemplate($this->tpl_mainpage);
        }
    }
    
    // 最初に開いた時の処理（メールアドレス入力画面）
    function lfForgotDefault() {
        // クッキー管理クラス
        $objCookie = new SC_Cookie(COOKIE_EXPIRE);
        $this->tpl_login_email = $objCookie->getCookie('login_email');
    }
    
    // メールアドレス確認（秘密の質問入力画面）
    function lfForgotMailCheck() {
        // パラメータ管理クラス,パラメータ情報の初期化
        $this->objFormParam = new SC_FormParam();
        $this->lfMailCheckInitParam();
        // POST値の取得
        $this->objFormParam->setParam($_POST);
        $this->objFormParam->convParam();
        $this->objFormParam->toLower('email');
        $this->arrForm = $this->objFormParam->getHashArray();
        //エラーチェック
        $this->arrErr = $this->lfMailCheckErrorCheck();
        if (count($this->arrErr) == 0) {
            $email = $this->arrForm['email'];
            $objQuery =& SC_Query::getSingletonInstance();
            $where = "(email Like ? OR email_mobile Like ?) AND name01 Like ? AND name02 Like ? AND del_flg = 0";
            $arrVal = array($this->arrForm['email'], $this->arrForm['email'], $this->arrForm['name01'], $this->arrForm['name02']);
            $result = $objQuery->select("reminder, status", "dtb_customer", $where, $arrVal);
            if (isset($result[0]['reminder']) and isset($this->arrReminder[$result[0]['reminder']])) {
                if($result[0]['status'] == '2') {
                    // 有効な情報であるため、秘密の質問確認へ遷移
                    $this->tpl_mainpage = 'forgot/secret.tpl';
                    $this->arrForm['reminder'] = $result[0]['reminder'];
                } else if ($result[0]['status'] == '1') {
                    $this->errmsg = 'ご入力のemailアドレスは現在仮登録中です。<br/>登録の際にお送りしたメールのURLにアクセスし、<br/>本会員登録をお願いします。';
                }
            } else {
                $this->errmsg = 'お名前に間違いがあるか、このメールアドレスは登録されていません。';
            }
        }
    }

    // メールアドレス確認におけるエラーチェック
    function lfMailCheckErrorCheck() {
        // 入力データを渡す
        $arrRet = $this->objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError();
        return $objErr->arrErr;
    }
    
    // メールアドレス確認におけるパラメーター情報の初期化
    function lfMailCheckInitParam() {
        $this->objFormParam->addParam("お名前(姓)", 'name01', STEXT_LEN, "aKV", array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("お名前(名)", 'name02', STEXT_LEN, "aKV", array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
        if ($this->isMobile == false){
            $this->objFormParam->addParam('メールアドレス', "email", MTEXT_LEN, "a", array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        } else {
            $this->objFormParam->addParam('メールアドレス', "email", MTEXT_LEN, "a", array("EXIST_CHECK", "EMAIL_CHECK", "NO_SPTAB" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK","MOBILE_EMAIL_CHECK"));
        }
        
    }

    // 秘密の質問確認
    function lfForgotSecretCheck() {
        // パラメータ管理クラス,パラメータ情報の初期化
        $this->objFormParam = new SC_FormParam();
        $this->lfSecretCheckInitParam();
        // POST値の取得
        $this->objFormParam->setParam($_POST);
        $this->objFormParam->convParam();
        $this->objFormParam->toLower('email');
        $this->arrForm = $this->objFormParam->getHashArray();
        //エラーチェック
        $this->arrErr = $this->lfSecretCheckErrorCheck();
        if (count($this->arrErr) == 0) {
            $email = $this->arrForm['email'];
            $objQuery =& SC_Query::getSingletonInstance();
            $where = "(email Like ? OR email_mobile Like ?) AND name01 Like ? AND name02 Like ? AND status = 2 AND del_flg = 0";
            $arrVal = array($this->arrForm['email'], $this->arrForm['email'], $this->arrForm['name01'], $this->arrForm['name02']);
            $result = $objQuery->select("customer_id, reminder, reminder_answer, salt", "dtb_customer", $where, $arrVal);
            if (isset($result[0]['reminder']) and isset($this->arrReminder[$result[0]['reminder']])
                    and $result[0]['reminder'] == $this->arrForm['reminder']) {
                
                if (SC_Utils_Ex::sfIsMatchHashPassword($this->arrForm['reminder_answer'], $result[0]['reminder_answer'], $result[0]['salt'])) {
                    // 秘密の答えが一致
                    // 新しいパスワードを設定する
                    $this->temp_password = GC_Utils_Ex::gfMakePassword(8);

                    if(FORGOT_MAIL == 1) {
                        // メールで変更通知をする
                        $objDb = new SC_Helper_DB_Ex();
                        $CONF = $objDb->sfGetBasisData();
                        $this->lfSendMail($CONF, $this->arrForm['email'], $this->arrForm['name01'], $this->temp_password);
                    }
                    $sqlval = array();
                    $sqlval['password'] = $this->temp_password;
                    SC_Helper_Customer_Ex::sfEditCustomerData($sqlval, $result[0]['customer_id']);

                    // 完了ページへ移動する
                    $this->tpl_mainpage = 'forgot/complete.tpl';
                    // transactionidの都合で呼び出し元をリロード。
                    $this->tpl_onload .= 'opener.location.reload(true);';
                } else {
                    // 秘密の答えが一致しなかった
                    $this->tpl_mainpage = 'forgot/secret.tpl';
                    $this->errmsg = '秘密の質問が一致しませんでした。';
                }
            } else {
                //不正なアクセス
                SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, "", true);
            }
        } else {
            $this->tpl_mainpage = 'forgot/secret.tpl';
        }
    }
    
    function lfSecretCheckInitParam() {
        // メールチェックと同等のチェックを再度行う
        $this->lfMailCheckInitParam();
        // 秘密の質問チェックの追加
        $this->objFormParam->addParam("パスワード確認用の質問", "reminder", STEXT_LEN, "n", array("EXIST_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("パスワード確認用の質問の答え", "reminder_answer", STEXT_LEN, "aKV", array("EXIST_CHECK","SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
    }

    // 秘密の答え確認におけるエラーチェック
    function lfSecretCheckErrorCheck() {
        // 入力データを渡す
        $arrRet = $this->objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError();
        return $objErr->arrErr;
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
     * @param string $temp_password 変更後のパスワード
     * @return void
     *
     * FIXME: メールテンプレート編集の方に足すのが望ましい？
     */
    function lfSendMail($CONF, $email, $customer_name, $temp_password){
        //　パスワード変更お知らせメール送信
        $this->customer_name = $customer_name;
        $this->temp_password = $temp_password;
        $objMailText = new SC_SiteView(false);
        $objMailText->assignobj($this);

        $toCustomerMail = $objMailText->fetch("mail_templates/forgot_mail.tpl");
        $objMail = new SC_SendMail();

        $objMail->setItem(
                              ''								//　宛先
                            , "パスワードが変更されました" ."【" .$CONF["shop_name"]. "】"		//　サブジェクト
                            , $toCustomerMail					//　本文
                            , $CONF["email03"]					//　配送元アドレス
                            , $CONF["shop_name"]				//　配送元　名前
                            , $CONF["email03"]					//　reply_to
                            , $CONF["email04"]					//　return_path
                            , $CONF["email04"]					//  Errors_to

                                                            );
        $objMail->setTo($email, $customer_name ." 様");
        $objMail->sendMail();
    }

    /**
     * モバイル空メール用のトークン作成
     *
     * @return void
     *
     * FIXME: この処理の有効性が不明
     */
    function createMobileToken() {
        $objMobile = new SC_Helper_Mobile_Ex();
        // 空メール用のトークンを作成。
        if (MOBILE_USE_KARA_MAIL) {
            $token = $objMobile->gfPrepareKaraMail('forgot/' . DIR_INDEX_URL);
            if ($token !== false) {
                $objPage->tpl_kara_mail_to = MOBILE_KARA_MAIL_ADDRESS_USER . MOBILE_KARA_MAIL_ADDRESS_DELIMITER . 'forgot_' . $token . '@' . MOBILE_KARA_MAIL_ADDRESS_DOMAIN;
            }
        }
    }
}
?>
