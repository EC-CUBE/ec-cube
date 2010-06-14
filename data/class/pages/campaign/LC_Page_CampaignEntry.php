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
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * キャンペーンエントリー のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_CampaignEntry extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'entry/index.tpl';                // メインテンプレート
        $this->tpl_title .= '会員登録(入力ページ)';             // ページタイトル

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData("mtb_pref",
                                array("pref_id", "pref_name", "rank"));
        $this->arrJob = $masterData->getMasterData("mtb_job");
        $this->arrReminder = $masterData->getMasterData("mtb_reminder");
        
        // 生年月日選択肢の取得
        $objDate = new SC_Date(START_BIRTH_YEAR, date("Y",strtotime("now")));
        $this->arrYear = $objDate->getYear('', 1950, '');
        $this->arrMonth = $objDate->getMonth(true);
        $this->arrDay = $objDate->getDay(true);
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        global $objCampaignSess;

        $objConn = new SC_DbConn();
        $objQuery = new SC_Query();
        $objView = new SC_SiteView();
        $CONF = $objView->objSiteInfo->data;
        $objCampaignSess = new SC_CampaignSession();

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
                                 array(  "column" => "email02", "convert" => "a" ),
                                 array(  "column" => "email_mobile", "convert" => "a" ),
                                 array(  "column" => "email_mobile02", "convert" => "a" ),
                                 array(  "column" => "tel01", "convert" => "n" ),
                                 array(  "column" => "tel02", "convert" => "n" ),
                                 array(  "column" => "tel03", "convert" => "n" ),
                                 array(  "column" => "fax01", "convert" => "n" ),
                                 array(  "column" => "fax02", "convert" => "n" ),
                                 array(  "column" => "fax03", "convert" => "n" ),
                                 array(  "column" => "sex", "convert" => "n" ),
                                 array(  "column" => "job", "convert" => "n" ),
                                 array(  "column" => "birth", "convert" => "n" ),
                                 array(  "column" => "year",  "convert" => "n"),
                                 array(  "column" => "month", "convert" => "n"),
                                 array(  "column" => "day",   "convert" => "n"),
                                 array(  "column" => "reminder", "convert" => "n" ),
                                 array(  "column" => "reminder_answer", "convert" => "aKV"),
                                 array(  "column" => "password", "convert" => "a" ),
                                 array(  "column" => "password02", "convert" => "a" ),
                                 array(  "column" => "mailmaga_flg", "convert" => "n" )
                                 );

        //---- 登録除外用カラム配列
        $arrRejectRegistColumn = array("year", "month", "day", "email02", "email_mobile02", "password02");

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            //-- POSTデータの引き継ぎ
            $this->arrForm = $_POST;

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
                    $this->passlen = $this->lfPassLen($passlen);

                    $this->tpl_mainpage = 'entry/confirm.tpl';
                    $this->tpl_title = '会員登録(確認ページ)';

                }

                //--仮登録と完了画面
                if ($_POST["mode"] == "complete") {
                    $this->uniqid = $this->lfRegistData ($this->arrForm, $arrRegistColumn, $arrRejectRegistColumn);

                    if($objCampaignSess->getIsCampaign()) {
                        $this->etc_value = "&cp=".$objCampaignSess->getCampaignId();
                    }

                    $this->tpl_css = '/css/layout/entry/complete.css';
                    $this->tpl_mainpage = 'entry/complete.tpl';
                    $this->tpl_title = '会員登録(完了ページ)';


                    // 仮登録完了メール送信
                    $this->CONF = $CONF;
                    $this->name01 = $_POST['name01'];
                    $this->name02 = $_POST['name02'];
                    $objMailText = new SC_SiteView();
                    $objMailText->assignobj($this);
                    $objHelperMail = new SC_Helper_Mail_Ex();
                    $objQuery = new SC_Query();

                    $subject = $objHelperMail->sfMakeSubject('会員登録のご確認');

                    $toCustomerMail = $objMailText->fetch("mail_templates/customer_mail.tpl");
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

                    // キャンペーン受注情報を登録
                    $this->lfRegistCampaignOrder($this->uniqid, $objQuery);

                    // 完了ページに移動させる。
                    $this->sendRedirect($this->getLocation("./complete.php"));
                    exit;
                }
            }
        }

        //---- ページ表示
        $objView->assignobj($this);
        // フレームを選択(キャンペーンページから遷移なら変更)
        $objCampaignSess->pageView($objView);

    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    //---- function群
    function lfRegistData ($array, $arrRegistColumn, $arrRejectRegistColumn) {
        $objConn = new SC_DbConn();

        // 仮登録
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

        $count = 1;
        while ($count != 0) {
            $uniqid = SC_Utils_Ex::sfGetUniqRandomId("t");
            $count = $objConn->getOne("SELECT COUNT(*) FROM dtb_customer WHERE secret_key = ?", array($uniqid));
        }

        $arrRegist["secret_key"] = $uniqid;		// 仮登録ID発行
        $arrRegist["create_date"] = "now()"; 	// 作成日
        $arrRegist["update_date"] = "now()"; 	// 更新日
        $arrRegist["first_buy_date"] = "";	 	// 最初の購入日

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
            if(strlen(($array[$key])) > 0) {
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
            $array['email'] = strtolower($array['email']);
            $objQuery = new SC_Query();
            $arrRet = $objQuery->select("email, update_date, del_flg", "dtb_customer","email = ? ORDER BY del_flg", array($array["email"]));

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

        $objErr->doFunc(array("お電話番号1", 'tel01'), array("EXIST_CHECK","SPTAB_CHECK" ));
        $objErr->doFunc(array("お電話番号2", 'tel02'), array("EXIST_CHECK","SPTAB_CHECK" ));
        $objErr->doFunc(array("お電話番号3", 'tel03'), array("EXIST_CHECK","SPTAB_CHECK" ));
        $objErr->doFunc(array("お電話番号", "tel01", "tel02", "tel03") ,array("TEL_CHECK"));
        $objErr->doFunc(array("FAX番号1", 'fax01'), array("SPTAB_CHECK"));
        $objErr->doFunc(array("FAX番号2", 'fax02'), array("SPTAB_CHECK"));
        $objErr->doFunc(array("FAX番号3", 'fax03'), array("SPTAB_CHECK"));
        $objErr->doFunc(array("FAX番号", "fax01", "fax02", "fax03") ,array("TEL_CHECK"));
        $objErr->doFunc(array("ご性別", "sex") ,array("SELECT_CHECK", "NUM_CHECK"));
        $objErr->doFunc(array("パスワード", 'password', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "SPTAB_CHECK" ,"ALNUM_CHECK", "NUM_RANGE_CHECK"));
        $objErr->doFunc(array("パスワード(確認)", 'password02', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "SPTAB_CHECK" ,"ALNUM_CHECK", "NUM_RANGE_CHECK"));
        $objErr->doFunc(array('パスワード', 'パスワード(確認)', "password", "password02") ,array("EQUAL_CHECK"));
        $objErr->doFunc(array("パスワードを忘れたときのヒント 質問", "reminder") ,array("SELECT_CHECK", "NUM_CHECK"));
        $objErr->doFunc(array("パスワードを忘れたときのヒント 答え", "reminder_answer", STEXT_LEN) ,array("EXIST_CHECK","SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("メールマガジン", "mailmaga_flg") ,array("SELECT_CHECK", "NUM_CHECK"));
        $objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_BIRTHDAY"));


        return $objErr->arrErr;
    }

    // キャンペーン受注テーブルへ登録
    function lfRegistCampaignOrder($uniqid, &$objQuery) {
        global $objCampaignSess;

        $campaign_id = $objCampaignSess->getCampaignId();

        // 顧客データを取得
        $cols = "
            customer_id,
            name01 as order_name01,
            name02 as order_name02,
            kana01 as order_kana01,
            kana02 as order_kana02,
            zip01 as order_zip01,
            zip02 as order_zip02,
            pref as order_pref,
            addr01 as order_addr01,
            addr02 as order_addr02,
            email as order_email,
            tel01 as order_tel01,
            tel02 as order_tel02,
            tel03 as order_tel03,
            fax01 as order_fax01,
            fax02 as order_fax02,
            fax03 as order_fax03,
            sex as order_sex,
            job as order_job,
            birth as order_birth
            ";

        $arrCustomer = $objQuery->select($cols, "dtb_customer", "secret_key = ?", array($uniqid));

        $sqlval = $arrCustomer[0];
        $sqlval['campaign_id'] = $campaign_id;
        $sqlval['create_date'] = 'now()';

        // INSERTの実行
        $objQuery->insert("dtb_campaign_order", $sqlval);

        // 申し込み数の更新
        $total_count = $objQuery->get("dtb_campaign", "total_count", "campaign_id = ?", array($campaign_id));
        $arrCampaign['total_count'] = $total_count += 1;
        $objQuery->update("dtb_campaign", $arrCampaign, "campaign_id = ?", array($campaign_id));
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

}
?>
