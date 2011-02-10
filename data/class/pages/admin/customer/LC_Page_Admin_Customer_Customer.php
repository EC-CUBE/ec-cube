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
require_once(CLASS_REALDIR . "pages/admin/LC_Page_Admin.php");

/**
 * 顧客登録 のページクラス
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Customer_Customer extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'customer/customer.tpl';
        $this->tpl_mainno = 'customer';
        $this->tpl_subnavi = 'customer/subnavi.tpl';
        $this->tpl_subno = 'index';
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_subtitle = '顧客登録';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrJob = $masterData->getMasterData("mtb_job");
        $this->arrSex = $masterData->getMasterData("mtb_sex");
        $this->arrReminder = $masterData->getMasterData("mtb_reminder");
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
        // 認証可否の判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        $this->objQuery = new SC_Query();
        $objDate = new SC_Date(1901);
        $this->arrYear = $objDate->getYear();    //　日付プルダウン設定
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();

        //---- 登録用カラム配列
        $arrRegistColumn = array(
                                 array(  "column" => "name01",        "convert" => "aKV" ),
                                 array(  "column" => "name02",        "convert" => "aKV" ),
                                 array(  "column" => "kana01",        "convert" => "CKV" ),
                                 array(  "column" => "kana02",        "convert" => "CKV" ),
                                 array(  "column" => "zip01",        "convert" => "n" ),
                                 array(  "column" => "zip02",        "convert" => "n" ),
                                 array(  "column" => "pref",        "convert" => "n" ),
                                 array(  "column" => "addr01",        "convert" => "aKV" ),
                                 array(  "column" => "addr02",        "convert" => "aKV" ),
                                 array(  "column" => "email",        "convert" => "a" ),
                                 array(  "column" => "email_mobile",    "convert" => "a" ),
                                 array(  "column" => "tel01",        "convert" => "n" ),
                                 array(  "column" => "tel02",        "convert" => "n" ),
                                 array(  "column" => "tel03",        "convert" => "n" ),
                                 array(  "column" => "fax01",        "convert" => "n" ),
                                 array(  "column" => "fax02",        "convert" => "n" ),
                                 array(  "column" => "fax03",        "convert" => "n" ),
                                 array(  "column" => "sex",            "convert" => "n" ),
                                 array(  "column" => "job",            "convert" => "n" ),
                                 array(  "column" => "birth",        "convert" => "n" ),
                                 array(  "column" => "password",    "convert" => "a" ),
                                 array(  "column" => "reminder",    "convert" => "n" ),
                                 array(  "column" => "reminder_answer", "convert" => "aKV" ),
                                 array(  "column" => "mailmaga_flg", "convert" => "n" ),
                                 array(  "column" => "note",        "convert" => "aKV" ),
                                 array(  "column" => "point",        "convert" => "n" ),
                                 array(  "column" => "status",        "convert" => "n" )
                                 );

        //---- 登録除外用カラム配列
        $arrRejectRegistColumn = array("year", "month", "day");
        
        //-- POSTデータの引き継ぎ
        $this->arrForm = $_POST;
        $this->arrForm['email'] = strtolower($this->arrForm['email']); // emailはすべて小文字で処理
        
        //----　顧客情報編集
        switch ($this->getMode()) {
        case 'confirm':
            //-- 入力データの変換
            $this->arrForm = $this->lfConvertParam($this->arrForm, $arrRegistColumn);
            //-- 入力チェック
            $this->arrErr = $this->lfErrorCheck($this->arrForm);
            // エラーなしの場合
            if(count($this->arrErr) == 0) {
                $this->tpl_mainpage = 'customer/customer_confirm.tpl'; // 確認ページ
                $passlen = strlen($this->arrForm['password']);
                $this->passlen = SC_Utils_Ex::sfPassLen($passlen);
            } else {
                foreach($this->arrForm as $key => $val) {
                    $this->list_data[ $key ] = $val;
                }
            }
            break;
        case 'complete':
            $this->tpl_mainpage = 'customer/customer_complete.tpl';
            
            // シークレット№も登録する。
            $secret = SC_Utils_Ex::sfGetUniqRandomId("r");
            $this->arrForm['secret_key'] = $secret;
            array_push($arrRegistColumn, array('column' => 'secret_key', 'convert' => 'n'));
            
            //-- 登録
            $this->lfRegisData($this->arrForm, $arrRegistColumn);
            break;
        case 'return':
            foreach($this->arrForm as $key => $val) {
                $this->list_data[ $key ] = $val;
            }
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

    // 編集登録
    function lfRegisData($array, $arrRegistColumn) {

        foreach ($arrRegistColumn as $data) {
            if($array[$data["column"]] != "") {
                $arrRegist[$data["column"]] = $array[$data["column"]];
            } else {
                $arrRegist[$data["column"]] = NULL;
            }
        }
        if (strlen($array["year"]) > 0) {
            $arrRegist["birth"] = $array["year"] ."/". $array["month"] ."/". $array["day"] ." 00:00:00";
        }

        //-- パスワード/リマインダーの答え暗号化。
        $salt = SC_Utils_Ex::sfGetRandomString(10);
        $arrRegist["salt"] = $salt;
        $arrRegist["password"] = SC_Utils_Ex::sfGetHashString($array["password"], $salt);
        $arrRegist["reminder_answer"] = SC_Utils_Ex::sfGetHashString($arrRegist["reminder_answer"], $salt);

        $arrRegist["update_date"] = "Now()";

        //-- 編集登録実行
        $this->objQuery->begin();
        $arrRegist['customer_id'] = $this->objQuery->nextVal('dtb_customer_customer_id');
        $this->objQuery->Insert("dtb_customer", $arrRegist);
        $this->objQuery->commit();
    }


    //----　取得文字列の変換
    function lfConvertParam($array, $arrRegistColumn) {
        /*
         *    文字列の変換
         *    K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *    C :  「全角ひら仮名」を「全角かた仮名」に変換
         *    V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *    n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
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

        $objErr->doFunc(array("会員状態", 'status'), array("EXIST_CHECK"));
        $objErr->doFunc(array("お名前(姓)", 'name01', STEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お名前(名)", 'name02', STEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("お名前(フリガナ・姓)", 'kana01', STEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array("お名前(フリガナ・名)", 'kana02', STEXT_LEN), array("EXIST_CHECK", "MAX_LENGTH_CHECK", "KANA_CHECK"));
        $objErr->doFunc(array("郵便番号1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
        $objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));
        $objErr->doFunc(array("都道府県", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
        $objErr->doFunc(array("住所（1）", "addr01", MTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("住所（2）", "addr02", MTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('メールアドレス', "email", MTEXT_LEN) ,array("EXIST_CHECK", "NO_SPTAB", "EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));

        //現会員の判定 →　現会員もしくは仮登録中は、メアド一意が前提になってるので同じメアドで登録不可
        if (strlen($array["email"]) > 0) {
            $array['email'] = strtolower($array['email']);
            $sql = "SELECT customer_id FROM dtb_customer WHERE (email ILIKE ? escape '#' OR email_mobile ILIKE ? escape '#') AND (status = 1 OR status = 2) AND del_flg = 0 AND customer_id <> ?";
            $checkMail = ereg_replace( "_", "#_", $array["email"]);
            $result = $this->objQuery->getAll($sql, array($checkMail, $checkMail, $array["customer_id"]));
            if (count($result) > 0) {
                $objErr->arrErr["email"] .= "※ すでに登録されているメールアドレスです。<br />";
            }
        }

        $objErr->doFunc(array('メールアドレス(モバイル)', "email_mobile", MTEXT_LEN) ,array("EMAIL_CHECK", "EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        //現会員の判定 →　現会員もしくは仮登録中は、メアド一意が前提になってるので同じメアドで登録不可
        if (strlen($array["email_mobile"]) > 0) {
            $array['email_mobile'] = strtolower($array['email_mobile']);
            $sql = "SELECT customer_id FROM dtb_customer WHERE (email ILIKE ? escape '#' OR email_mobile ILIKE ? escape '#') AND (status = 1 OR status = 2) AND del_flg = 0 AND customer_id <> ?";
            $checkMail = ereg_replace( "_", "#_", $array["email_mobile"]);
            $result = $this->objQuery->getAll($sql, array($checkMail, $checkMail, $array["customer_id"]));
            if (count($result) > 0) {
                $objErr->arrErr["email_mobile"] .= "※ すでに登録されているメールアドレス(モバイル)です。<br />";
            }
        }


        $objErr->doFunc(array("お電話番号1", 'tel01'), array("EXIST_CHECK"));
        $objErr->doFunc(array("お電話番号2", 'tel02'), array("EXIST_CHECK"));
        $objErr->doFunc(array("お電話番号3", 'tel03'), array("EXIST_CHECK"));
        $objErr->doFunc(array("お電話番号", "tel01", "tel02", "tel03") ,array("TEL_CHECK"));
        $objErr->doFunc(array("FAX番号", "fax01", "fax02", "fax03") ,array("TEL_CHECK"));
        $objErr->doFunc(array("ご性別", "sex") ,array("SELECT_CHECK", "NUM_CHECK"));
        $objErr->doFunc(array("ご職業", "job") ,array("NUM_CHECK"));
        $objErr->doFunc(array("パスワード", 'password', PASSWORD_LEN1, PASSWORD_LEN2), array("EXIST_CHECK", "ALNUM_CHECK", "NUM_RANGE_CHECK"));
        $objErr->doFunc(array("パスワードを忘れたときのヒント 質問", "reminder") ,array("SELECT_CHECK", "NUM_CHECK"));
        $objErr->doFunc(array("パスワードを忘れたときのヒント 答え", "reminder_answer", STEXT_LEN) ,array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("メールマガジン", "mailmaga_flg") ,array("SELECT_CHECK", "NUM_CHECK"));
        $objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_DATE"));
        $objErr->doFunc(array("SHOP用メモ", 'note', LTEXT_LEN), array("MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("所持ポイント", "point", TEL_LEN) ,array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        return $objErr->arrErr;

    }

}
?>
