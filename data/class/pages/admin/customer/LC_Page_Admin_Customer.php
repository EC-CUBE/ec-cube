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
 * 顧客管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Customer extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'customer/index.tpl';
        $this->tpl_mainno = 'customer';
        $this->tpl_subnavi = 'customer/subnavi.tpl';
        $this->tpl_subno = 'index';
        $this->tpl_pager = TEMPLATE_REALDIR . 'admin/pager.tpl';
        $this->tpl_subtitle = '顧客マスタ';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');

        $this->arrJob = $masterData->getMasterData("mtb_job");
        $this->arrJob["不明"] = "不明";
        $this->arrSex = $masterData->getMasterData("mtb_sex");
        $this->arrPageRows = $masterData->getMasterData("mtb_page_rows");
        $this->arrMAILMAGATYPE = $masterData->getMasterData("mtb_mail_magazine_type");
        $this->arrHtmlmail[''] = "すべて";
        $this->arrHtmlmail[1] = $this->arrMAILMAGATYPE[1];
        $this->arrHtmlmail[2] = $this->arrMAILMAGATYPE[2];

        $this->arrStatus[1] = "仮会員";
        $this->arrStatus[2] = "本会員";

        //---- CSVダウンロード用
        $this->arrColumnCSV = array(
            array(
                "sql" => "customer_id",
                "csv" => "customer_id",
                "header" => "顧客ID"
            ),
            array(
                "sql" => "name01",
                "csv" => "name01",
                "header" => "名前1"
            ),
            array(
                "sql" => "name02",
                "csv" => "name02",
                "header" => "名前2"
            ),
            array(
                "sql" => "kana01",
                "csv" => "kana01",
                "header" => "お名前(フリガナ・姓)"
            ),
            array(
                "sql" => "kana02",
                "csv" => "kana02",
                "header" => "お名前(フリガナ・名)"
            ),
            array(
                "sql" => "zip01",
                "csv" => "zip01",
                "header" => "郵便番号1"
            ),
            array(
                "sql" => "zip02",
                "csv" => "zip02",
                "header" => "郵便番号2"
            ),
            array(
                "sql" => "pref",
                "csv" => "pref",
                "header" => "都道府県"
            ),
            array(
                "sql" => "addr01",
                "csv" => "addr01",
                "header" => "住所1"
            ),
            array(
                "sql" => "addr02",
                "csv" => "addr02",
                "header" => "住所2"
            ),
            array(
                "sql" => "email",
                "csv" => "email",
                "header" => "E-MAIL"
            ),
            array(
                "sql" => "tel01",
                "csv" => "tel01",
                "header" => "TEL1"
            ),
            array(
                "sql" => "tel02",
                "csv" => "tel02",
                "header" => "TEL2"
            ),
            array(
                "sql" => "tel03",
                "csv" => "tel03",
                "header" => "TEL3"
            ),
            array(
                "sql" => "fax01",
                "csv" => "fax01",
                "header" => "FAX1"
            ),
            array(
                "sql" => "fax02",
                "csv" => "fax02",
                "header" => "FAX2"
            ),
            array(
                "sql" => "fax03",
                "csv" => "fax03",
                "header" => "FAX3"
            ),
            array(
                "sql" => "CASE WHEN sex = 1 THEN '男性' ELSE '女性' END AS sex",
                "csv" => "sex",
                "header" => "性別"
            ),
            array(
                "sql" => "job",
                "csv" => "job",
                "header" => "職業"
            ),
            array(
                "sql" => "cast(birth as date) AS birth",
                "csv" => "birth",
                "header" => "誕生日"
            ),
            array(
                "sql" => "cast(first_buy_date as date) AS first_buy_date",
                "csv" => "first_buy_date",
                "header" => "初回購入日"
            ),
            array(
                "sql" => "cast(last_buy_date as date) AS last_buy_date",
                "csv" => "last_buy_date",
                "header" => "最終購入日"
            ),
            array(
                "sql" => "buy_times",
                "csv" => "buy_times",
                "header" => "購入回数"
            ),
            array(
                "sql" => "point",
                "csv" => "point",
                "header" => "ポイント残高"
            ),
            array(
                "sql" => "note",
                "csv" => "note",
                "header" => "備考"
            ),
            array(
                "sql" => "cast(create_date as date) AS create_date",
                "csv" => "create_date",
                "header" => "登録日"
            ),
            array(
                "sql" => "cast(update_date as date) AS update_date",
                "csv" => "update_date",
                "header" => "更新日"
            ),
        );
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
        //---- ページ初期設定
        $objQuery = new SC_Query();
        $objView = new SC_AdminView();
        $objDate = new SC_Date(1901);
        $objDb = new SC_Helper_DB_Ex();
        $this->arrYear = $objDate->getYear();   //　日付プルダウン設定
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();
        $this->objDate = $objDate;

        // 認証可否の判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        // POST値の引き継ぎ
        $this->arrForm = $_POST;

        // ページ送り用
        $this->arrHidden['search_pageno'] =
                isset($_POST['search_pageno']) ? $_POST['search_pageno'] : "";

        // 検索ワードの引き継ぎ
        foreach ($_POST as $key => $val) {
            switch($key) {
            case 'sex':
            case 'status':
                $this->arrHidden[$key] = SC_Utils_Ex::sfMergeParamCheckBoxes($val);
                if(!is_array($val)) {
                    $this->arrForm[$key] = split("-", $val);
                }
                break;
            default:
                $this->arrHidden[$key] = $val;
                break;
            }
        }

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        // 顧客削除
        if ($_POST['mode'] == "delete") {
            $sql = "SELECT status,email FROM dtb_customer WHERE customer_id = ? AND del_flg = 0";
            $result_customer = $objQuery->getAll($sql, array($_POST["edit_customer_id"]));

            if ($result_customer[0]["status"] == 2) {           //本会員削除
                $arrDel = array("del_flg" => 1, "update_date" => "NOW()");
                $objQuery->conn->autoExecute("dtb_customer", $arrDel, "customer_id = " . SC_Utils_Ex::sfQuoteSmart($_POST["edit_customer_id"]) );
            } elseif ($result_customer[0]["status"] == 1) {     //仮会員削除
                $sql = "DELETE FROM dtb_customer WHERE customer_id = ?";
                $objQuery->query($sql, array($_POST["edit_customer_id"]));
            }
        }
        // 登録メール再送
        if ($_POST['mode'] == "resend_mail") {
            $arrRet = $objQuery->select("name01, name02, secret_key, email, email_mobile", "dtb_customer","customer_id = ? AND del_flg <> 1 AND status = 1", array($_POST["edit_customer_id"]));
            if( is_array($arrRet) === true && count($arrRet) > 0 ){

                $this->name01 = $arrRet[0]['name01'];
                $this->name02 = $arrRet[0]['name02'];
                $this->uniqid = $arrRet[0]['secret_key'];

                $CONF = $objDb->sfGetBasisData();
                $this->CONF = $CONF;
                /**
                 * 携帯メールアドレスが登録されていれば携帯サイトから仮会員登録したものと判定する。
                 * TODO: とりあえずの簡易的な判定なので、将来的には判定ルーチンを修正した方が良い。
                 */
                if (!empty($arrRet[0]['email_mobile'])) {
                    $objMailText = new SC_MobileView(false);
                    $this->to_name01 = $arrRet[0]['name01'];
                    $this->to_name02 = $arrRet[0]['name02'];
                } else {
                    $objMailText = new SC_SiteView(false);
                }
                $objMailText->assignobj($this);
                $mailHelper = new SC_Helper_Mail_Ex();

                $subject = $mailHelper->sfMakesubject('会員登録のご確認');
                $toCustomerMail = $objMailText->fetch("mail_templates/customer_mail.tpl");

                $objMail = new SC_SendMail();
                $objMail->setItem(
                                    ''                  //　宛先
                                    , $subject          //　サブジェクト
                                    , $toCustomerMail   //　本文
                                    , $CONF["email03"]  //　配送元アドレス
                                    , $CONF["shop_name"]//　配送元　名前
                                    , $CONF["email03"]  //　reply_to
                                    , $CONF["email04"]  //　return_path
                                    , $CONF["email04"]  //  Errors_to
                                 );
                // 宛先の設定
                $name = $this->name01 . $this->name02 ." 様";
                $objMail->setTo($arrRet[0]["email"], $name);
                $objMail->sendMail();
            }

        }

        if ($_POST['mode'] == "search" || $_POST['mode'] == "csv"  || $_POST['mode'] == "delete" || $_POST['mode'] == "delete_all" || $_POST['mode'] == "resend_mail") {

            // 入力文字の強制変換
            $this->lfConvertParam();
            // エラーチェック
            $this->arrErr = $this->lfCheckError($this->arrForm);

            $where = "del_flg = 0";

            /* 入力エラーなし */
            if (count($this->arrErr) == 0) {

                //-- 検索データ取得
                $objSelect = new SC_CustomerList($this->arrForm, "customer");

                // 表示件数設定
                $page_rows = $this->arrForm['page_rows'];
                if(is_numeric($page_rows)) {
                    $page_max = $page_rows;
                } else {
                    $page_max = SEARCH_PMAX;
                }

                if (!isset($this->arrForm['search_pageno'])) $this->arrForm['search_pageno'] = "";

                if ($this->arrForm['search_pageno'] == 0){
                    $this->arrForm['search_pageno'] = 1;
                }

                $offset = $page_max * ($this->arrForm['search_pageno'] - 1);
                $objSelect->setLimitOffset($page_max, $offset);

                if ($_POST["mode"] == 'csv') {
                    $searchSql = $objSelect->getListCSV($this->arrColumnCSV);
                }else{
                    $searchSql = $objSelect->getList();
                }

                $this->search_data = $objQuery->getAll($searchSql, $objSelect->arrVal);

                switch($_POST['mode']) {
                case 'csv':
                    require_once(CLASS_EX_REALDIR . "helper_extends/SC_Helper_CSV_Ex.php");

                    $objCSV = new SC_Helper_CSV_Ex();
                    $i = 0;
                    $header = "";

                    // CSVカラム取得
                    $arrCsvOutput = ($objCSV->sfgetCsvOutput(2, 'status = 1'));

                    if (count($arrCsvOutput) <= 0) break;

                    foreach($arrCsvOutput as $data) {
                        $arrColumn[] = $data["col"];
                        if ($i != 0) $header .= ", ";
                        $header .= $data["disp_name"];
                        $i ++;
                    }
                    $header .= "\n";

                    //-　都道府県/職業の変換
                    for($i = 0; $i < count($this->search_data); $i ++) {
                        $this->search_data[$i]["pref"] = $this->arrPref[ $this->search_data[$i]["pref"] ];
                        $this->search_data[$i]["job"]  = $this->arrJob[ $this->search_data[$i]["job"] ];
                    }

                    //-　CSV出力
                    $data = SC_Utils_Ex::getCSVData($this->search_data, $arrColumn);


                    // CSVを送信する。
                    list($fime_name, $data) = SC_Utils_Ex::sfGetCSVData($head.$data);
                    $this->sendResponseCSV($fime_name, $data);
                    exit;
                    break;
                case 'delete_all':
                    // 検索結果をすべて削除
                    $where = "product_id IN (SELECT product_id FROM SC_Product::alldtlSQL() WHERE $where)";
                    $sqlval['del_flg'] = 1;
                    $objQuery->update("dtb_products", $sqlval, $where, $arrval);

                    $sql = "SELECT status,email FROM dtb_customer WHERE customer_id = ? AND del_flg = 0";
                    $result_customer = $objQuery->getAll($sql, array($_POST["del_customer_id"]));

                    if ($result_customer[0]["status"] == 2) {           //本会員削除
                        $arrDel = array("del_flg" => 1, "update_date" => "NOW()");
                        $objQuery->conn->autoExecute("dtb_customer", $arrDel, "customer_id = " . SC_Utils_Ex::sfQuoteSmart($_POST["del_customer_id"]) );
                    } elseif ($result_customer[0]["status"] == 1) {     //仮会員削除
                        $sql = "DELETE FROM dtb_customer WHERE customer_id = ?";
                        $objQuery->query($sql, array($_POST["del_customer_id"]));
                    }

                    break;
                default:

                    // 行数の取得
                    $linemax = $objQuery->getOne( $objSelect->getListCount(), $objSelect->arrVal);
                    $this->tpl_linemax = $linemax;              // 何件が該当しました。表示用

                    // ページ送りの取得
                    $objNavi = new SC_PageNavi($this->arrHidden['search_pageno'],
                                               $linemax, $page_max,
                                               "fnCustomerPage", NAVI_PMAX);
                    $startno = $objNavi->start_row;
                    $this->arrPagenavi = $objNavi->arrPagenavi;
                }
            }
        }

        $this->arrCatList = $objDb->sfGetCategoryList();
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    //----　取得文字列の変換
    function lfConvertParam() {
        /*
         *  文字列の変換
         *  K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *  C :  「全角ひら仮名」を「全角かた仮名」に変換
         *  V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *  n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
         *  a :  全角英数字を半角英数字に変換する
         */
        // カラム名とコンバート情報
        $arrConvList['customer_id'] = "n" ;
        $arrConvList['name'] = "aKV" ;
        $arrConvList['pref'] = "n" ;
        $arrConvList['kana'] = "CKV" ;
        $arrConvList['b_start_year'] = "n" ;
        $arrConvList['b_start_month'] = "n" ;
        $arrConvList['b_start_day'] = "n" ;
        $arrConvList['b_end_year'] = "n" ;
        $arrConvList['b_end_month'] = "n" ;
        $arrConvList['b_end_day'] = "n" ;
        $arrConvList['tel'] = "n" ;
        $arrConvList['birth_month'] = "n" ;
        $arrConvList['email'] = "a" ;
        $arrConvList['buy_total_from'] = "n" ;
        $arrConvList['buy_total_to'] = "n" ;
        $arrConvList['buy_times_from'] = "n" ;
        $arrConvList['buy_times_to'] = "n" ;
        $arrConvList['start_year'] = "n" ;
        $arrConvList['start_month'] = "n" ;
        $arrConvList['start_day'] = "n" ;
        $arrConvList['end_year'] = "n" ;
        $arrConvList['end_month'] = "n" ;
        $arrConvList['end_day'] = "n" ;
        $arrConvList['page_rows'] = "n" ;
        $arrConvList['buy_start_year'] = "n" ;      //　最終購入日 START 年
        $arrConvList['buy_start_month'] = "n" ;     //　最終購入日 START 月
        $arrConvList['buy_start_day'] = "n" ;       //　最終購入日 START 日
        $arrConvList['buy_end_year'] = "n" ;            //　最終購入日 END 年
        $arrConvList['buy_end_month'] = "n" ;       //　最終購入日 END 月
        $arrConvList['buy_end_day'] = "n" ;         //　最終購入日 END 日
        $arrConvList['buy_product_name'] = "aKV" ;  //　購入商品名
        $arrConvList['buy_product_code'] = "aKV" ;  //　購入商品コード
        $arrConvList['category_id'] = "" ;          //　カテゴリ

        // 文字変換
        foreach ($arrConvList as $key => $val) {
            // POSTされてきた値のみ変換する。
            if(isset($this->arrForm[$key])) {
                $this->arrForm[$key] = mb_convert_kana($this->arrForm[$key] ,$val);
            }
        }
    }

    //---- 入力エラーチェック
    function lfCheckError($array) {

        $objErr = new SC_CheckError($array);

        $objErr->doFunc(array("顧客コード", "customer_id", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("都道府県", "pref", 2), array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("顧客名", "name", STEXT_LEN), array("MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("顧客名(カナ)", "kana", STEXT_LEN), array("SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANABLANK_CHECK"));
        $objErr->doFunc(array("誕生日(開始日)", "b_start_year", "b_start_month", "b_start_day"), array("CHECK_DATE"));
        $objErr->doFunc(array("誕生日(終了日)", "b_end_year", "b_end_month", "b_end_day"), array("CHECK_DATE"));
        $objErr->doFunc(array("誕生日(開始日)","誕生日(終了日)", "b_start_year", "b_start_month", "b_start_day", "b_end_year", "b_end_month", "b_end_day"), array("CHECK_SET_TERM"));
        $objErr->doFunc(array("誕生月", "birth_month", 2), array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('メールアドレス', "email", STEXT_LEN) ,array("EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array('携帯メールアドレス', "email_mobile", STEXT_LEN) ,array("EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("電話番号", "tel", TEL_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("購入金額(開始)", "buy_total_from", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("購入金額(終了)", "buy_total_to", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
        if ( (is_numeric($array["buy_total_from"]) && is_numeric($array["buy_total_to"]) ) && ($array["buy_total_from"] > $array["buy_total_to"]) ) $objErr->arrErr["buy_total_from"] .= "※ 購入金額の指定範囲が不正です。";
        $objErr->doFunc(array("購入回数(開始)", "buy_times_from", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("購入回数(終了)", "buy_times_to", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
        if ( (is_numeric($array["buy_times_from"]) && is_numeric($array["buy_times_to"]) ) && ($array["buy_times_from"] > $array["buy_times_to"]) ) $objErr->arrErr["buy_times_from"] .= "※ 購入回数の指定範囲が不正です。";
        $objErr->doFunc(array("登録・更新日(開始日)", "start_year", "start_month", "start_day",), array("CHECK_DATE"));
        $objErr->doFunc(array("登録・更新日(終了日)", "end_year", "end_month", "end_day"), array("CHECK_DATE"));
        $objErr->doFunc(array("登録・更新日(開始日)","登録・更新日(終了日)", "start_year", "start_month", "start_day", "end_year", "end_month", "end_day"), array("CHECK_SET_TERM"));
        $objErr->doFunc(array("表示件数", "page_rows", 3), array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("最終購入日(開始日)", "buy_start_year", "buy_start_month", "buy_start_day",), array("CHECK_DATE"));   //最終購入日(開始日)
        $objErr->doFunc(array("最終購入(終了日)", "buy_end_year", "buy_end_month", "buy_end_day"), array("CHECK_DATE"));            //最終購入日(終了日)
        //購入金額(from) ＞ 購入金額(to) の場合はエラーとする
        $objErr->doFunc(array("最終購入日(開始日)","登録・更新日(終了日)", "buy_start_year", "buy_start_month", "buy_start_day", "buy_end_year", "buy_end_month", "buy_end_day"), array("CHECK_SET_TERM"));
        $objErr->doFunc(array("購入商品コード", "buy_product_code", STEXT_LEN), array("MAX_LENGTH_CHECK"));                     //購入商品コード
        $objErr->doFunc(array("購入商品名", "buy_product_name", STEXT_LEN), array("MAX_LENGTH_CHECK"));                         //購入商品名称

        return $objErr->arrErr;
    }
}
?>
