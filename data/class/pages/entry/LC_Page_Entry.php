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

/**
 * 会員登録(入力ページ) のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Entry.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_Entry extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'entry/index.tpl';
        $this->tpl_title .= '会員登録(入力ページ)';
        $this->year = "";
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData("mtb_pref",
                                array("pref_id", "pref_name", "rank"));
        $this->arrJob = $masterData->getMasterData("mtb_job");
        $this->arrReminder = $masterData->getMasterData("mtb_reminder");
        $this->objDate = new SC_Date(START_BIRTH_YEAR, date("Y",strtotime("now")));
        $this->arrYear = $this->objDate->getYear('', 1950);	//　日付プルダウン設定
        $this->arrMonth = $this->objDate->getMonth();
        $this->arrDay = $this->objDate->getDay();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        global $objCampaignSess;

        $objConn = new SC_DbConn();
        $objView = new SC_SiteView();
        $objCustomer = new SC_Customer();
        $objCampaignSess = new SC_CampaignSession();
        $objDb = new SC_Helper_DB_Ex();
        $CONF = $objDb->sf_getBasisData();
        $objDate = new SC_Date(START_BIRTH_YEAR, date("Y",strtotime("now")));

        $ssl_url  = rtrim(SSL_URL,"/");
        $ssl_url .= $_SERVER['PHP_SELF'];


        // 規約ページからの遷移でなければエラー画面へ遷移する
        if (empty($_POST) && !preg_match('/kiyaku.php/', basename($_SERVER['HTTP_REFERER']))) {
            SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, "", true);
        }

        // レイアウトデザインを取得
        $layout = new SC_Helper_PageLayout_Ex();
        $layout->sfGetPageLayout($this, false, DEF_LAYOUT);

        //---- 登録用カラム配列
        $arrRegistColumn = array(
                                     array(  "column" => "name01", "convert" => "aKV" ),
                                     array(  "column" => "name02", "convert" => "aKV" ),
                                     array(  "column" => "kana01", "convert" => "CKV" ),
                                     array(  "column" => "kana02", "convert" => "CKV" ),
                                     array(  "column" => "zip01", "convert" => "n" ),
                                     array(  "column" => "zip02", "convert" => "n" ),
                                     array(  "column" => "pref", "convert" => "n" ),
                                     array(  "column" => "addr01", "convert" => "aKV" ),
                                     array(  "column" => "addr02", "convert" => "aKV" ),
                                     array(  "column" => "email", "convert" => "a" ),
                                     array(  "column" => "email2", "convert" => "a" ),
                                     array(  "column" => "email_mobile", "convert" => "a" ),
                                     array(  "column" => "email_mobile2", "convert" => "a" ),
                                     array(  "column" => "tel01", "convert" => "n" ),
                                     array(  "column" => "tel02", "convert" => "n" ),
                                     array(  "column" => "tel03", "convert" => "n" ),
                                     array(  "column" => "fax01", "convert" => "n" ),
                                     array(  "column" => "fax02", "convert" => "n" ),
                                     array(  "column" => "fax03", "convert" => "n" ),
                                     array(  "column" => "sex", "convert" => "n" ),
                                     array(  "column" => "job", "convert" => "n" ),
                                     array(  "column" => "birth", "convert" => "n" ),
                                     array(  "column" => "reminder", "convert" => "n" ),
                                     array(  "column" => "reminder_answer", "convert" => "aKV"),
                                     array(  "column" => "password", "convert" => "a" ),
                                     array(  "column" => "password02", "convert" => "a" ),
                                     array(  "column" => "mailmaga_flg", "convert" => "n" ),
                                 );

        //---- 登録除外用カラム配列
        $arrRejectRegistColumn = array("year", "month", "day", "email02", "email_mobile02", "password02");

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (!$this->isValidToken()) {
                SC_Utils_Ex::sfDispSiteError(PAGE_ERROR, "", true);
            }

            //空白・改行の削除
            $_POST["name01"] = preg_replace('/^[ 　\r\n]*(.*?)[ 　\r\n]*$/u', '$1', $_POST["name01"]);
            $_POST["name02"] = preg_replace('/^[ 　\r\n]*(.*?)[ 　\r\n]*$/u', '$1', $_POST["name02"]);
            $_POST["kana01"] = preg_replace('/^[ 　\r\n]*(.*?)[ 　\r\n]*$/u', '$1', $_POST["kana01"]);
            $_POST["kana02"] = preg_replace('/^[ 　\r\n]*(.*?)[ 　\r\n]*$/u', '$1', $_POST["kana02"]);
            $_POST["zip01"] = preg_replace('/^[ 　\r\n]*(.*?)[ 　\r\n]*$/u', '$1', $_POST["zip01"]);
            $_POST["zip02"] = preg_replace('/^[ 　\r\n]*(.*?)[ 　\r\n]*$/u', '$1', $_POST["zip02"]);
            $_POST["addr01"] = preg_replace('/^[ 　\r\n]*(.*?)[ 　\r\n]*$/u', '$1', $_POST["addr01"]);
            $_POST["addr02"] = preg_replace('/^[ 　\r\n]*(.*?)[ 　\r\n]*$/u', '$1', $_POST["addr02"]);
            $_POST["tel01"] = preg_replace('/^[ 　\r\n]*(.*?)[ 　\r\n]*$/u', '$1', $_POST["tel01"]);
            $_POST["tel02"] = preg_replace('/^[ 　\r\n]*(.*?)[ 　\r\n]*$/u', '$1', $_POST["tel02"]);
            $_POST["tel03"] = preg_replace('/^[ 　\r\n]*(.*?)[ 　\r\n]*$/u', '$1', $_POST["tel03"]);
            $_POST["fax01"] = preg_replace('/^[ 　\r\n]*(.*?)[ 　\r\n]*$/u', '$1', $_POST["fax01"]);
            $_POST["fax02"] = preg_replace('/^[ 　\r\n]*(.*?)[ 　\r\n]*$/u', '$1', $_POST["fax02"]);
            $_POST["fax03"] = preg_replace('/^[ 　\r\n]*(.*?)[ 　\r\n]*$/u', '$1', $_POST["fax03"]);
            $_POST["email"] = preg_replace('/^[ 　\r\n]*(.*?)[ 　\r\n]*$/u', '$1', $_POST["email"]);
            $_POST["email02"] = preg_replace('/^[ 　\r\n]*(.*?)[ 　\r\n]*$/u', '$1', $_POST["email02"]);
            $_POST["password"] = preg_replace('/^[ 　\r\n]*(.*?)[ 　\r\n]*$/u', '$1', $_POST["password"]);
            $_POST["password02"] = preg_replace('/^[ 　\r\n]*(.*?)[ 　\r\n]*$/u', '$1', $_POST["password02"]);
            $_POST["reminder_answer"] = preg_replace('/^[ 　\r\n]*(.*?)[ 　\r\n]*$/u', '$1', $_POST["reminder_answer"]);

            //-- POSTデータの引き継ぎ
            $this->arrForm = $_POST;

            //SSL用
            $this->arrForm[ssl_url] = $ssl_url;

            if($this->arrForm['year'] == '----') {
                $this->arrForm['year'] = '';
            }

            $this->arrForm['email'] = strtolower($this->arrForm['email']);		// emailはすべて小文字で処理
            $this->arrForm['email02'] = strtolower($this->arrForm['email02']);	// emailはすべて小文字で処理

            //-- 入力データの変換
            $this->arrForm = $this->lfConvertParam($this->arrForm, $arrRegistColumn);

            //--　入力エラーチェック
            $this->arrErr = $this->lfErrorCheck($this->arrForm);

            if ($this->arrErr || $_POST["mode"] == "return") {		// 入力エラーのチェック
                foreach($arrRegistColumn as $key) {
                    $this->$key['column'] = $this->arrForm[$key['column']];
                }

            } else {

                //--　確認
                if ($_POST["mode"] == "confirm") {
                    foreach($this->arrForm as $key => $val) {
                        if ($key != "mode" && $key != "subm") $this->list_data[ $key ] = $val;
                    }
                    //パスワード表示
                    $passlen = strlen($this->arrForm['password']);
                    $this->passlen = SC_Utils_Ex::lfPassLen($passlen);

                    $this->tpl_css = '/css/layout/entry/confirm.css';
                    $this->tpl_mainpage = 'entry/confirm.tpl';
                    $this->tpl_title = '会員登録(確認ページ)';

                }

                //--　会員登録と完了画面
                if ($_POST["mode"] == "complete") {
                    // キャンペーンからの遷移の時用の値
                    if($objCampaignSess->getIsCampaign()) {
                        $this->etc_value = "&cp=".$objCampaignSess->getCampaignId();
                    }

                    // 会員情報の登録
                    $this->CONF = $CONF;
                    $this->uniqid = $this->lfRegistData ($this->arrForm, $arrRegistColumn, $arrRejectRegistColumn, CUSTOMER_CONFIRM_MAIL);

                    $this->tpl_css = '/css/layout/entry/complete.css';
                    $this->tpl_mainpage = 'entry/complete.tpl';
                    $this->tpl_title = '会員登録(完了ページ)';

                    //　完了メール送信
                    $this->name01 = $_POST['name01'];
                    $this->name02 = $_POST['name02'];
                    $objMailText = new SC_SiteView();
                    $objMailText->assignobj($this);

                    $mailHelper = new SC_Helper_Mail_Ex();
                    $objQuery = new SC_Query();
                    // 仮会員が有効の場合
                    if(CUSTOMER_CONFIRM_MAIL == true) {
                        $subject = $mailHelper->sfMakesubject($objQuery, $objMailText, $this, '会員登録のご確認');
                        $toCustomerMail = $objMailText->fetch("mail_templates/customer_mail.tpl");
                    } else {
                        $subject = $mailHelper->sfMakesubject($objQuery, $objMailText, $this, '会員登録のご完了');
                        $toCustomerMail = $objMailText->fetch("mail_templates/customer_regist_mail.tpl");
                        // ログイン状態にする
                        $objCustomer->setLogin($_POST["email"]);
                    }

                    $objMail = new SC_SendMail();
                    $objMail->setItem(
                                        ''									//　宛先
                                        , $subject							//　サブジェクト
                                        , $toCustomerMail					//　本文
                                        , $CONF["email03"]					//　配送元アドレス
                                        , $CONF["shop_name"]				//　配送元　名前
                                        , $CONF["email03"]					//　reply_to
                                        , $CONF["email04"]					//　return_path
                                        , $CONF["email04"]					//  Errors_to
                                    );
                    // 宛先の設定
                    $name = $_POST["name01"] . $_POST["name02"] ." 様";
                    $objMail->setTo($_POST["email"], $name);
                    $objMail->sendMail();

                    // 完了ページに移動させる。
                    $customer_id = $objQuery->get("dtb_customer", "customer_id", "secret_key = ?", array($this->uniqid));
                    $this->sendRedirect($this->getLocation("./complete.php", array("ci" => $customer_id)));
                    exit;
                }
            }
        }

        if($this->year == '') {
            $this->year = '----';
        }

        $this->transactionid = $this->getToken();

        //----　ページ表示
        $objView->assignobj($this);
        // フレームを選択(キャンペーンページから遷移なら変更)
        $objCampaignSess->pageView($objView);
    }

    /**
     * モバイルページを初期化する.
     *
     * @return void
     */
    function mobileInit() {
        $this->init();
        $this->tpl_mainpage = 'entry/index.tpl';		// メインテンプレート
        $this->tpl_title .= '会員登録(1/3)';			//　ページタイトル
    }

    /**
     * Page のプロセス(モバイル).
     *
     * @return void
     */
    function mobileProcess() {
        //---- ページ初期設定
        $objDb = new SC_Helper_DB_Ex();
        $objMobile = new SC_Helper_Mobile_Ex();
        $CONF = $objDb->sf_getBasisData();					// 店舗基本情報
        $objConn = new SC_DbConn();
        $objView = new SC_MobileView();
        $objCustomer = new SC_Customer();
        $objDate = new SC_Date(START_BIRTH_YEAR, date("Y",strtotime("now")));

        // 空メール
        if (isset($_SESSION['mobile']['kara_mail_from'])) {
            $_POST['email'] = $_SESSION['mobile']['kara_mail_from'];
            $this->tpl_kara_mail_from = $_POST['email'];
        } elseif (MOBILE_USE_KARA_MAIL) {
            $token = $objMobile->gfPrepareKaraMail('entry/index.php');
            if ($token !== false) {
                $this->tpl_mainpage = 'entry/mail.tpl';
                $this->tpl_title = '会員登録(空メール)';
                $this->tpl_kara_mail_to = MOBILE_KARA_MAIL_ADDRESS_USER . MOBILE_KARA_MAIL_ADDRESS_DELIMITER . 'entry_' . $token . '@' . MOBILE_KARA_MAIL_ADDRESS_DOMAIN;
                $this->tpl_from_address = $CONF['email03'];
            }
        }

        // レイアウトデザインを取得
        $objLayout = new SC_Helper_PageLayout_Ex();
        $objLayout->sfGetPageLayout($this, false, DEF_LAYOUT);

        //---- 登録用カラム配列
        $arrRegistColumn = array(
                                 array(  "column" => "name01", "convert" => "aKV" ),
                                 array(  "column" => "name02", "convert" => "aKV" ),
                                 array(  "column" => "kana01", "convert" => "CKV" ),
                                 array(  "column" => "kana02", "convert" => "CKV" ),
                                 array(  "column" => "zip01", "convert" => "n" ),
                                 array(  "column" => "zip02", "convert" => "n" ),
                                 array(  "column" => "pref", "convert" => "n" ),
                                 array(  "column" => "addr01", "convert" => "aKV" ),
                                 array(  "column" => "addr02", "convert" => "aKV" ),
                                 array(  "column" => "email", "convert" => "a" ),
                                 array(  "column" => "email2", "convert" => "a" ),
                                 array(  "column" => "email_mobile", "convert" => "a" ),
                                 array(  "column" => "email_mobile2", "convert" => "a" ),
                                 array(  "column" => "tel01", "convert" => "n" ),
                                 array(  "column" => "tel02", "convert" => "n" ),
                                 array(  "column" => "tel03", "convert" => "n" ),
                                 array(  "column" => "fax01", "convert" => "n" ),
                                 array(  "column" => "fax02", "convert" => "n" ),
                                 array(  "column" => "fax03", "convert" => "n" ),
                                 array(  "column" => "sex", "convert" => "n" ),
                                 array(  "column" => "job", "convert" => "n" ),
                                 array(  "column" => "birth", "convert" => "n" ),
                                 array(  "column" => "reminder", "convert" => "n" ),
                                 array(  "column" => "reminder_answer", "convert" => "aKV"),
                                 array(  "column" => "password", "convert" => "a" ),
                                 array(  "column" => "password02", "convert" => "a" ),
                                 array(  "column" => "mailmaga_flg", "convert" => "n" ),
                                 );

        //---- 登録除外用カラム配列
        $arrRejectRegistColumn = array("year", "month", "day", "email02", "email_mobile02", "password02");

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            //-- POSTデータの引き継ぎ
            $this->arrForm = $_POST;

            if(isset($this->arrForm['year'])
               && $this->arrForm['year'] == '----') {
                $this->arrForm['year'] = '';
            }

            $this->arrForm['email'] = strtolower($this->arrForm['email']);		// emailはすべて小文字で処理

            //-- 入力データの変換
            $this->arrForm = $this->lfConvertParam($this->arrForm, $arrRegistColumn);

            // 戻るボタン用処理
            if (!empty($_POST["return"])) {
                switch ($_POST["mode"]) {
                case "complete":
                    $_POST["mode"] = "set3";
                    break;
                case "confirm":
                    $_POST["mode"] = "set2";
                    break;
                default:
                    $_POST["mode"] = "set1";
                    break;
                }
            }

            //--　入力エラーチェック
            if ($_POST["mode"] == "set1") {
                $this->arrErr = $this->lfErrorCheck1($this->arrForm);
                $this->tpl_mainpage = 'entry/index.tpl';
                $this->tpl_title = '会員登録(1/3)';
            } elseif ($_POST["mode"] == "set2") {
                $this->arrErr = $this->lfErrorCheck2($this->arrForm);
                $this->tpl_mainpage = 'entry/set1.tpl';
                $this->tpl_title = '会員登録(2/3)';
            } else {
                $this->arrErr = $this->lfErrorCheck3($this->arrForm);
                $this->tpl_mainpage = 'entry/set2.tpl';
                $this->tpl_title = '会員登録(3/3)';
            }

            foreach($arrRegistColumn as $key) {
                $this->$key['column'] = $this->arrForm[$key['column']];
            }

            if ($this->arrErr || !empty($_POST["return"])) {		// 入力エラーのチェック

                //-- データの設定
                if ($_POST["mode"] == "set1") {
                    $checkVal = array("email", "password", "reminder", "reminder_answer", "name01", "name02", "kana01", "kana02");
                } elseif ($_POST["mode"] == "set2") {
                    $checkVal = array("sex", "year", "month", "day", "zip01", "zip02");
                } else {
                    $checkVal = array("pref", "addr01", "addr02", "tel01", "tel02", "tel03", "mailmaga_flg");
                }

                foreach($this->arrForm as $key => $val) {
                    if ($key != "mode" && $key != "submit" && $key != "return" && $key != session_name() && !in_array($key, $checkVal))
                        $this->list_data[ $key ] = $val;
                }



            } else {

                //--　テンプレート設定
                if ($_POST["mode"] == "set1") {
                    $this->tpl_mainpage = 'entry/set1.tpl';
                    $this->tpl_title = '会員登録(2/3)';
                } elseif ($_POST["mode"] == "set2") {
                    $this->tpl_mainpage = 'entry/set2.tpl';
                    $this->tpl_title = '会員登録(3/3)';

                    if (@$this->arrForm['pref'] == "" && @$this->arrForm['addr01'] == "" && @$this->arrForm['addr02'] == "") {
                        $address = $this->lfGetAddress($_REQUEST['zip01'].$_REQUEST['zip02']);
                        $this->pref = @$address[0]['state'];
                        $this->addr01 = @$address[0]['city'] . @$address[0]['town'];
                    }
                } elseif ($_POST["mode"] == "confirm") {
                    //パスワード表示
                    $passlen = strlen($this->arrForm['password']);
                    $this->passlen = $this->lfPassLen($passlen);

                    //メール受け取り
                    if (!isset($this->arrForm['mailmaga_flg'])) $this->arrForm['mailmaga_flg']  = "";
                    if (strtolower($this->arrForm['mailmaga_flg']) == "on") {
                        $this->arrForm['mailmaga_flg']  = "2";
                    } else {
                        $this->arrForm['mailmaga_flg']  = "3";
                    }

                    $this->tpl_mainpage = 'entry/confirm.tpl';
                    $this->tpl_title = '会員登録(確認ページ)';

                }

                //-- データ設定
                unset($this->list_data);
                if ($_POST["mode"] == "set1") {
                    $checkVal = array("sex", "year", "month", "day", "zip01", "zip02");
                } elseif ($_POST["mode"] == "set2") {
                    $checkVal = array("pref", "addr01", "addr02", "tel01", "tel02", "tel03", "mailmaga_flg");
                } else {
                    $checkVal = array();
                }

                foreach($this->arrForm as $key => $val) {
                    if ($key != "mode" && $key != "submit" && $key != "confirm" && $key != "return" && $key != session_name() && !in_array($key, $checkVal)) {
                        $this->list_data[ $key ] = $val;
                    }
                }


                //--　仮登録と完了画面
                if ($_POST["mode"] == "complete") {

                    // 確認画面で再度エラーチェックを行う。（画面1）
                    $arrErr = $this->lfErrorCheck1($this->arrForm);
                    if(count($arrErr) > 0){
                        $this->tpl_mainpage = 'entry/index.tpl';
                        $this->tpl_title = '会員登録(1/3)';
                        $this->arrErr = $arrErr;
                        //----　ページ表示
                        $objView->assignobj($this);
                        $objView->display(SITE_FRAME);
                        exit();
                    }

                    // 確認画面で再度エラーチェックを行う。（画面2）
                    $arrErr = $this->lfErrorCheck2($this->arrForm);
                    if(count($arrErr) > 0){
                        $this->tpl_mainpage = 'entry/set1.tpl';
                        $this->tpl_title = '会員登録(2/3)';
                        $this->arrErr = $arrErr;
                        //----　ページ表示
                        $objView->assignobj($this);
                        $objView->display(SITE_FRAME);
                        exit();
                    }

                    // 確認画面で再度エラーチェックを行う。（画面3）
                    $arrErr = $this->lfErrorCheck3($this->arrForm);
                    if(count($arrErr) > 0){
                        $this->tpl_mainpage = 'entry/set2.tpl';
                        $this->tpl_title = '会員登録(3/3)';
                        $this->arrErr = $arrErr;
                        //----　ページ表示
                        $objView->assignobj($this);
                        $objView->display(SITE_FRAME);
                        exit();
                    }

                    $this->CONF = $CONF;
                    $this->uniqid = $this->lfRegistData ($this->arrForm, $arrRegistColumn, $arrRejectRegistColumn, CUSTOMER_CONFIRM_MAIL, true, $this->arrForm["email"]);

                    // 空メールを受信済みの場合はすぐに本登録完了にする。
                    if (isset($_SESSION['mobile']['kara_mail_from'])) {
                        $param = array("mode" => "regist",
                                       "id" => $this->uniqid,
                                       session_name() => session_id());
                        $this->sendRedirect($this->getLocation(MOBILE_URL_DIR . "regist/index.php", $param));
                        exit;
                    }

                    $this->tpl_mainpage = 'entry/complete.tpl';
                    $this->tpl_title = '会員登録(完了ページ)';

                    $objMobile->sfMobileSetExtSessionId('id', $this->uniqid, 'regist/index.php');

                    //　仮登録完了メール送信
                    $this->to_name01 = $_POST['name01'];
                    $this->to_name02 = $_POST['name02'];
                    $objMailText = new SC_MobileView();
                    $objMailText->assignobj($this);
                    $objHelperMail = new SC_Helper_Mail_Ex();
                    $objQuery = new SC_Query();

                    // 仮会員が有効の場合
                    if(CUSTOMER_CONFIRM_MAIL == true) {
                        // Moba8パラメーターを保持する場合はカラム追加
                        if (isset($_SESSION['a8'])) $this->etc_value = "&a8=". $_SESSION['a8'];
                        $subject = $objHelperMail->sfMakeSubject($objQuery, $objMailText, $this, '会員登録のご確認');
                        $toCustomerMail = $objMailText->fetch("mail_templates/customer_mail.tpl");
                    } else {
                        $subject = $objHelperMail->sfMakesubject($objQuery, $objMailText, $this, '会員登録のご完了');
                        $toCustomerMail = $objMailText->fetch("mail_templates/customer_regist_mail.tpl");
                        // ログイン状態にする
                        $objCustomer->setLogin($_POST["email"]);
                    }

                    $objMail = new SC_SendMail();
                    $objMail->setItem(
                                      ''									//　宛先
                                      , $subject							//　サブジェクト
                                      , $toCustomerMail					//　本文
                                      , $CONF["email03"]					//　配送元アドレス
                                      , $CONF["shop_name"]				//　配送元　名前
                                      , $CONF["email03"]					//　reply_to
                                      , $CONF["email04"]					//　return_path
                                      , $CONF["email04"]					//  Errors_to
                                      , $CONF["email01"]					//  Bcc
                                      );
                    // 宛先の設定
                    $name = $_POST["name01"] . $_POST["name02"] ." 様";
                    $objMail->setTo($_POST["email"], $name);
                    $objMail->sendMail();

                    // 完了ページに移動させる。
                    $this->sendRedirect($this->getLocation("./complete.php"), true);
                    exit;
                }
            }
        }

        //----　ページ表示
        $objView->assignobj($this);
        $objView->display(SITE_FRAME);
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

    // 会員情報の登録
    function lfRegistData ($array, $arrRegistColumn, $arrRejectRegistColumn, $confirm_flg, $isMobile = false, $email_mobile = "") {
        $objConn = new SC_DbConn();

        // 登録データの生成
        foreach ($arrRegistColumn as $data) {
            if (strlen($array[ $data["column"] ]) > 0 && ! in_array($data["column"], $arrRejectRegistColumn)) {
                $arrRegist[ $data["column"] ] = $array[ $data["column"] ];
            }
        }

        // 誕生日が入力されている場合
        if (strlen($array["year"]) > 0 ) {
            $arrRegist["birth"] = $array["year"] ."/". $array["month"] ."/". $array["day"] ." 00:00:00";
        }

        // パスワードの暗号化
        $arrRegist["password"] = sha1($arrRegist["password"] . ":" . AUTH_MAGIC);

        // 仮会員登録の場合
        if($confirm_flg == true) {
            // 重複しない会員登録キーを発行する。
            $count = 1;
            while ($count != 0) {
                $uniqid = SC_Utils_Ex::sfGetUniqRandomId("t");
                $count = $objConn->getOne("SELECT COUNT(*) FROM dtb_customer WHERE secret_key = ?", array($uniqid));
            }
            switch($array["mailmaga_flg"]) {
                case 1:
                    $arrRegist["mailmaga_flg"] = 4;
                    break;
                case 2:
                    $arrRegist["mailmaga_flg"] = 5;
                    break;
                default:
                    $arrRegist["mailmaga_flg"] = 6;
                    break;
            }

            $arrRegist["status"] = "1";				// 仮会員
        } else {
            // 重複しない会員登録キーを発行する。
            $count = 1;
            while ($count != 0) {
                $uniqid = SC_Utils_Ex::sfGetUniqRandomId("r");
                $count = $objConn->getOne("SELECT COUNT(*) FROM dtb_customer WHERE secret_key = ?", array($uniqid));
            }
            $arrRegist["status"] = "2";				// 本会員
        }

        /*
          secret_keyは、テーブルで重複許可されていない場合があるので、
          本会員登録では利用されないがセットしておく。
        */
        $arrRegist["secret_key"] = $uniqid;		// 会員登録キー
        $arrRegist["create_date"] = "now()"; 	// 作成日
        $arrRegist["update_date"] = "now()"; 	// 更新日
        $arrRegist["first_buy_date"] = "";	 	// 最初の購入日
        $arrRegist["point"] = $this->CONF["welcome_point"]; // 入会時ポイント

        if ($isMobile) {
            // 携帯メールアドレス
            $arrRegist['email_mobile'] = $arrRegist['email'];
            //PHONE_IDを取り出す
            $phoneId = SC_MobileUserAgent::getId();
            $arrRegist['mobile_phone_id'] =  $phoneId;
        }

        //-- 仮登録実行
        $objConn->query("BEGIN");

        $objQuery = new SC_Query();
        $objQuery->insert("dtb_customer", $arrRegist);


    /* メルマガ会員機能は現在停止中　2007/03/07


        //--　非会員でメルマガ登録しているかの判定
        $sql = "SELECT count(*) FROM dtb_customer_mail WHERE email = ?";
        $mailResult = $objConn->getOne($sql, array($arrRegist["email"]));

        //--　メルマガ仮登録実行
        $arrRegistMail["email"] = $arrRegist["email"];
        if ($array["mailmaga_flg"] == 1) {
            $arrRegistMail["mailmaga_flg"] = 4;
        } elseif ($array["mailmaga_flg"] == 2) {
            $arrRegistMail["mailmaga_flg"] = 5;
        } else {
            $arrRegistMail["mailmaga_flg"] = 6;
        }
        $arrRegistMail["update_date"] = "now()";

        // 非会員でメルマガ登録している場合
        if ($mailResult == 1) {
            $objQuery->update("dtb_customer_mail", $arrRegistMail, "email = '" .addslashes($arrRegistMail["email"]). "'");
        } else {				//　新規登録の場合
            $arrRegistMail["create_date"] = "now()";
            $objQuery->insert("dtb_customer_mail", $arrRegistMail);
        }
    */
        $objConn->query("COMMIT");

        return $uniqid;
    }

    //----　取得文字列の変換
    function lfConvertParam($array, $arrRegistColumn) {
        /*
         *	文字列の変換
         *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *	C :  「全角ひら仮名」を「全角かた仮名」に変換
         *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
         *  a :  全角英数字を半角英数字に変換する
         */
        // カラム名とコンバート情報
        foreach ($arrRegistColumn as $data) {
            $arrConvList[ $data["column"] ] = $data["convert"];
        }
        // 文字変換
        foreach ($arrConvList as $key => $val) {
            // POSTされてきた値のみ変換する。
            if(isset($array[$key]) && strlen($array[$key]) > 0) {
                $array[$key] = mb_convert_kana($array[$key] ,$val);
            }
        }
        return $array;
    }

    //---- 入力エラーチェック
    function lfErrorCheck($array) {

        $objErr = new SC_CheckError($array);

        $objErr->doFunc(array("お名前（姓）", 'name01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お名前（名）", 'name02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("フリガナ（セイ）", 'kana01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array("フリガナ（メイ）", 'kana02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array("郵便番号1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
        $objErr->doFunc(array("都道府県", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
        $objErr->doFunc(array("ご住所1", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("ご住所2", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('メールアドレス', "email", MTEXT_LEN) ,array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('メールアドレス(確認)', "email02", MTEXT_LEN) ,array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK","SPTAB_CHECK" , "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('メールアドレス', 'メールアドレス(確認)', "email", "email02") ,array("EQUAL_CHECK"));

        //現会員の判定 →　現会員もしくは仮登録中は、メアド一意が前提になってるので同じメアドで登録不可
        if (strlen($array["email"]) > 0) {
            $array["email"] = strtolower($array["email"]);
            $objQuery = new SC_Query();
            $arrRet = $objQuery->select("email, update_date, del_flg", "dtb_customer","email = ? ORDER BY del_flg", array($array["email"]));

            if(count($arrRet) > 0) {
                if($arrRet[0]['del_flg'] != '1') {
                    // 会員である場合
                    if (!isset($objErr->arrErr['email'])) $objErr->arrErr['email'] = "";
                    $objErr->arrErr["email"] .= "※ すでに会員登録で使用されているメールアドレスです。<br />";
                } else {
                    // 退会した会員である場合
                    $leave_time = SC_Utils_Ex::sfDBDatetoTime($arrRet[0]['update_date']);
                    $now_time = time();
                    $pass_time = $now_time - $leave_time;
                    // 退会から何時間-経過しているか判定する。
                    $limit_time = ENTRY_LIMIT_HOUR * 3600;
                    if($pass_time < $limit_time) {
                        if (!isset($objErr->arrErr['email'])) $objErr->arrErr['email'] = "";
                        $objErr->arrErr["email"] .= "※ 退会から一定期間の間は、同じメールアドレスを使用することはできません。<br />";
                    }
                }
            }
        }

        $objErr->doFunc(array("お電話番号1", 'tel01'), array("EXIST_CHECK","SPTAB_CHECK" ));
        $objErr->doFunc(array("お電話番号2", 'tel02'), array("EXIST_CHECK","SPTAB_CHECK" ));
        $objErr->doFunc(array("お電話番号3", 'tel03'), array("EXIST_CHECK","SPTAB_CHECK" ));
        $objErr->doFunc(array("お電話番号", "tel01", "tel02", "tel03",TEL_ITEM_LEN) ,array("TEL_CHECK"));
        $objErr->doFunc(array("FAX番号1", 'fax01'), array("SPTAB_CHECK"));
        $objErr->doFunc(array("FAX番号2", 'fax02'), array("SPTAB_CHECK"));
        $objErr->doFunc(array("FAX番号3", 'fax03'), array("SPTAB_CHECK"));
        $objErr->doFunc(array("FAX番号", "fax01", "fax02", "fax03", TEL_ITEM_LEN) ,array("TEL_CHECK"));
        $objErr->doFunc(array("ご性別", "sex") ,array("SELECT_CHECK", "NUM_CHECK"));
        $objErr->doFunc(array("パスワード", 'password', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "SPTAB_CHECK" ,"ALNUM_CHECK", "NUM_RANGE_CHECK"));
        $objErr->doFunc(array("パスワード(確認)", 'password02', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "SPTAB_CHECK" ,"ALNUM_CHECK", "NUM_RANGE_CHECK"));
        $objErr->doFunc(array('パスワード', 'パスワード(確認)', "password", "password02") ,array("EQUAL_CHECK"));
        $objErr->doFunc(array("パスワードを忘れたときのヒント 質問", "reminder") ,array("SELECT_CHECK", "NUM_CHECK"));
        $objErr->doFunc(array("パスワードを忘れたときのヒント 答え", "reminder_answer", STEXT_LEN) ,array("EXIST_CHECK","SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("メールマガジン", "mailmaga_flg") ,array("SELECT_CHECK", "NUM_CHECK"));

        $objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_DATE"));
        $objErr->doFunc(array("メールマガジン", 'mailmaga_flg'), array("SELECT_CHECK"));
        return $objErr->arrErr;
    }

    //確認ページ用パスワード表示用

    function lfPassLen($passlen){
        $ret = "";
        for ($i=0;$i<$passlen;true){
        $ret.="*";
        $i++;
        }
        return $ret;
    }

    // }}}
    // {{{ mobile functions

    // 郵便番号から住所の取得
    function lfGetAddress($zipcode) {

        $conn = new SC_DBconn(ZIP_DSN);

        // 郵便番号検索文作成
        $zipcode = mb_convert_kana($zipcode ,"n");
        $sqlse = "SELECT state, city, town FROM mtb_zip WHERE zipcode = ?";

        $data_list = $conn->getAll($sqlse, array($zipcode));

        // インデックスと値を反転させる。
        $arrREV_PREF = array_flip($this->arrPref);

        /*
         総務省からダウンロードしたデータをそのままインポートすると
         以下のような文字列が入っているので	対策する。
         ・（１・１９丁目）
         ・以下に掲載がない場合
        */
        $town =  $data_list[0]['town'];
        $town = ereg_replace("（.*）$","",$town);
        $town = ereg_replace("以下に掲載がない場合","",$town);
        $data_list[0]['town'] = $town;
        $data_list[0]['state'] = $arrREV_PREF[$data_list[0]['state']];

        return $data_list;
    }

    //---- 入力エラーチェック
    function lfErrorCheck1($array) {

        $objErr = new SC_CheckError($array);
        $objDb = new SC_Helper_DB_Ex();

        $objErr->doFunc(array("お名前（姓）", 'name01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お名前（名）", 'name02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お名前（カナ/姓）", 'kana01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array("お名前（カナ/名）", 'kana02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array('メールアドレス', "email", MTEXT_LEN) ,array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK", "MOBILE_EMAIL_CHECK"));

        //現会員の判定 →　現会員もしくは仮登録中は、メアド一意が前提になってるので同じメアドで登録不可
        if (strlen($array["email"]) > 0) {
            $array['email'] = strtolower($array['email']);
            $objQuery = new SC_Query();
            $arrRet = $objQuery->select("email, update_date, del_flg", "dtb_customer","email = ? OR email_mobile = ? ORDER BY del_flg", array($array["email"], $array["email"]));

            if(count($arrRet) > 0) {
                if($arrRet[0]['del_flg'] != '1') {
                    // 会員である場合
                    $objErr->arrErr["email"] .= "※ すでに会員登録で使用されているメールアドレスです。<br />";
                } else {
                    // 退会した会員である場合
                    $leave_time = SC_Utils_Ex::sfDBDatetoTime($arrRet[0]['update_date']);
                    $now_time = time();
                    $pass_time = $now_time - $leave_time;
                    // 退会から何時間-経過しているか判定する。
                    $limit_time = ENTRY_LIMIT_HOUR * 3600;
                    if($pass_time < $limit_time) {
                        $objErr->arrErr["email"] .= "※ 退会から一定期間の間は、同じメールアドレスを使用することはできません。<br />";
                    }
                }
            }
        }

        $objErr->doFunc(array("パスワード", 'password', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "SPTAB_CHECK" ,"ALNUM_CHECK", "NUM_RANGE_CHECK"));
        $objErr->doFunc(array("パスワード確認用の質問", "reminder") ,array("SELECT_CHECK", "NUM_CHECK"));
        $objErr->doFunc(array("パスワード確認用の質問の答え", "reminder_answer", STEXT_LEN) ,array("EXIST_CHECK","SPTAB_CHECK" , "MAX_LENGTH_CHECK"));

        return $objErr->arrErr;
    }

    //---- 入力エラーチェック
    function lfErrorCheck2($array) {
        $objErr = new SC_CheckError($array);

        $objErr->doFunc(array("郵便番号1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));

        $objErr->doFunc(array("性別", "sex") ,array("SELECT_CHECK", "NUM_CHECK"));
        $objErr->doFunc(array("生年月日 (年)", "year", 4), array("EXIST_CHECK", "SPTAB_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        if (!isset($objErr->arrErr['year']) && strlen($array['year'] > 0)) {
            $objErr->doFunc(array("生年月日 (年)", "year", $this->objDate->getStartYear()), array("MIN_CHECK"));
            $objErr->doFunc(array("生年月日 (年)", "year", $this->objDate->getEndYear()), array("MAX_CHECK"));
        }
        $objErr->doFunc(array("生年月日 (月日)", "month", "day"), array("SELECT_CHECK"));
        if (!isset($objErr->arrErr['year']) && !isset($objErr->arrErr['month']) && !isset($objErr->arrErr['day'])) {
            $objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_DATE"));
        }

        return $objErr->arrErr;
    }

    //---- 入力エラーチェック
    function lfErrorCheck3($array) {
        $objErr = new SC_CheckError($array);

        $objErr->doFunc(array("都道府県", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
        $objErr->doFunc(array("市区町村", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("番地", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("電話番号1", 'tel01'), array("EXIST_CHECK","SPTAB_CHECK" ));
        $objErr->doFunc(array("電話番号2", 'tel02'), array("EXIST_CHECK","SPTAB_CHECK" ));
        $objErr->doFunc(array("電話番号3", 'tel03'), array("EXIST_CHECK","SPTAB_CHECK" ));
        $objErr->doFunc(array("電話番号", "tel01", "tel02", "tel03",TEL_ITEM_LEN) ,array("TEL_CHECK"));

        return $objErr->arrErr;
    }

}
?>
