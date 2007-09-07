<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * メルマガ管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Mail extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'mail/index.tpl';
        $this->tpl_mainno = 'mail';
        $this->tpl_subnavi = 'mail/subnavi.tpl';
        $this->tpl_subno = "index";
        $this->tpl_pager = DATA_PATH . 'Smarty/templates/admin/pager.tpl';
        $this->tpl_subtitle = '配信内容設定';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData("mtb_pref", array("pref_id", "pref_name", "rank"));
        $this->arrJob = $masterData->getMasterData("mtb_job");
        $this->arrJob["不明"] = "不明";
        $this->arrSex = $masterData->getMasterData("mtb_sex");
        $this->arrMailType = $masterData->getMasterData("mtb_mail_type");
        $this->arrPageRows = $masterData->getMasterData("mtb_page_rows");
        // ページナビ用
        $this->tpl_pageno = isset($_POST['search_pageno']) ? $_POST['search_pageno'] : "";
        $this->arrMAILMAGATYPE = $masterData->getMasterData("mtb_mail_magazine_type");
        $this->arrHtmlmail[''] = "すべて";
        $this->arrHtmlmail[1] = $this->arrMAILMAGATYPE[1];
        $this->arrHtmlmail[2] = $this->arrMAILMAGATYPE[2];

        //---- 検索用項目配列
        $this->arrHtmlmail = array( "" => "両方",  1 => "HTML", 2 => "TEXT" );


        //---- 配列内容専用項目の配列
        $this->arrRegistColumn = array(
              array(  "column" => "template_id",    "convert" => "n" ),
              array(  "column" => "mail_method",    "convert" => "n" ),
              array(  "column" => "send_year",      "convert" => "n" ),
              array(  "column" => "send_month",     "convert" => "n" ),
              array(  "column" => "send_day",       "convert" => "n" ),
              array(  "column" => "send_hour",      "convert" => "n" ),
              array(  "column" => "send_minutes",   "convert" => "n" ),
              array(  "column" => "subject",        "convert" => "aKV" ),
              array(  "column" => "body",           "convert" => "KV" )
              );

        //---- メルマガ会員種別
        $this->arrCustomerType = array(1 => "会員",
                                       2 => "非会員",
                                       //3 => "CSV登録"
                                       );

        //---- 検索項目
        $this->arrSearchColumn = array(
             array(  "column" => "name",                "convert" => "aKV"),
             array(  "column" => "pref",                "convert" => "n" ),
             array(  "column" => "kana",                "convert" => "CKV"),
             array(  "column" => "sex",                 "convert" => "" ),
             array(  "column" => "tel",                 "convert" => "n" ),
             array(  "column" => "job",                 "convert" => "" ),
             array(  "column" => "email",               "convert" => "a" ),
             array(  "column" => "email_mobile",        "convert" => "a" ),
             array(  "column" => "htmlmail",            "convert" => "n" ),
             array(  "column" => "customer",            "convert" => "" ),
             array(  "column" => "buy_total_from",      "convert" => "n" ),
             array(  "column" => "buy_total_to",        "convert" => "n" ),
             array(  "column" => "buy_times_from",      "convert" => "n" ),
             array(  "column" => "buy_times_to",        "convert" => "n" ),
             array(  "column" => "birth_month",         "convert" => "n" ),
             array(  "column" => "b_start_year",        "convert" => "n" ),
             array(  "column" => "b_start_month",       "convert" => "n" ),
             array(  "column" => "b_start_day",         "convert" => "n" ),
             array(  "column" => "b_end_year",          "convert" => "n" ),
             array(  "column" => "b_end_month",         "convert" => "n" ),
             array(  "column" => "b_end_day",           "convert" => "n" ),
             array(  "column" => "start_year",          "convert" => "n" ),
             array(  "column" => "start_month",         "convert" => "n" ),
             array(  "column" => "start_day",           "convert" => "n" ),
             array(  "column" => "end_year",            "convert" => "n" ),
             array(  "column" => "end_month",           "convert" => "n" ),
             array(  "column" => "end_day",             "convert" => "n" ),
             array(  "column" => "buy_start_year",      "convert" => "n" ),
             array(  "column" => "buy_start_month",     "convert" => "n" ),
             array(  "column" => "buy_start_day",       "convert" => "n" ),
             array(  "column" => "buy_end_year",        "convert" => "n" ),
             array(  "column" => "buy_end_month",       "convert" => "n" ),
             array(  "column" => "buy_end_day",         "convert" => "n" ),
             array(  "column" => "buy_product_code",    "convert" => "aKV" ),
             array(  "column" => "buy_product_name",    "convert" => "aKV" ),
             array(  "column" => "category_id",         "convert" => "" ),
             array(  "column" => "buy_total_from",      "convert" => "n" ),
             array(  "column" => "buy_total_to",        "convert" => "n" ),
             array(  "column" => "campaign_id",         "convert" => "" ),
             array(  "column" => "mail_type",           "convert" => "" )
             );

        if (!isset($_POST['htmlmail'])) $_POST['htmlmail'] = "";
        if (!isset($_POST['mail_type'])) $_POST['mail_type'] = "";
        if (!isset($_POST['mode'])) $_POST['mode'] = "";
        if (!isset($_POST['buy_product_code'])) $_POST['buy_product_code'] = "";
        if (!isset($_GET['mode'])) $_GET['mode'] = "";
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {

        //---- ページ初期設定
        $conn = new SC_DBConn();
        $objView = new SC_AdminView();
        $objDate = new SC_Date();
        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();
        $this->objDate = $objDate;
        $this->arrTemplate = $this->getTemplateList($conn);

        $objSess = new SC_Session();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        /*
         query:配信履歴「確認」
        */
        if ($_GET["mode"] == "query" && SC_Utils_Ex::sfCheckNumLength($_GET["send_id"])) {
            // 送信履歴より、送信条件確認画面
            $sql = "SELECT search_data FROM dtb_send_history WHERE send_id = ?";
            $result = $conn->getOne($sql, array($_GET["send_id"]));
            $tpl_path = "mail/query.tpl";

            $list_data = unserialize($result);

            // 都道府県を変換
            $list_data['pref_disp'] = $this->arrPref[$list_data['pref']];

            // 配信形式
            $list_data['htmlmail_disp'] = $this->arrHtmlmail[$list_data['htmlmail']];

            // 性別の変換
            if (count($list_data['sex']) > 0) {
                foreach($list_data['sex'] as $key => $val){
                    $list_data['sex'][$key] = $this->arrSex[$val];
                    $sex_disp .= $list_data['sex'][$key] . " ";
                }
                $list_data['sex_disp'] = $sex_disp;
            }

            // 職業の変換
            if (count($list_data['job']) > 0) {
                foreach($list_data['job'] as $key => $val){
                    $list_data['job'][$key] = $this->arrJob[$val];
                    $job_disp .= $list_data['job'][$key] . " ";
                }
                $list_data['job_disp'] = $job_disp;
            }

            // カテゴリ変換
            $arrCatList = $objDb->sfGetCategoryList();
            $list_data['category_name'] = $arrCatList[$list_data['category_id']];

            $this->list_data = $list_data;

            $objView->assignobj($this);
            $objView->display($tpl_path);
            exit;
        }

        if($_POST['mode'] == 'delete') {
        }

        switch($_POST['mode']) {
            /*
             search:「検索」ボタン
             back:検索結果画面「戻る」ボタン
            */
        case 'delete':
        case 'search':
        case 'back':
            //-- 入力値コンバート
            $this->list_data = $this->lfConvertParam($_POST, $arrSearchColumn);

            //-- 入力エラーのチェック
            $this->arrErr = $this->lfErrorCheck($this->list_data);

            //-- 検索開始
            if (!is_array($this->arrErr)) {
                $this->list_data['name'] = SC_Utils_Ex::sfManualEscape($this->list_data['name']);
                // hidden要素作成
                $this->arrHidden = $this->lfGetHidden($this->list_data);

                //-- 検索データ取得
                $objSelect = new SC_CustomerList($this->list_data, "magazine");
                // 生成されたWHERE文を取得する
                list($where, $arrval) = $objSelect->getWhere();

                // 「WHERE」部分を削除する。
                $where = ereg_replace("^WHERE", "", $where);

                // 検索結果の取得
                $from = "dtb_customer";

                // 行数の取得
                $linemax = $objQuery->count($from, $where, $arrval);
                $this->tpl_linemax = $linemax;               // 何件が該当しました。表示用

                // ページ送りの取得
                $objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, SEARCH_PMAX, "fnResultPageNavi", NAVI_PMAX);
                $this->arrPagenavi = $objNavi->arrPagenavi;
                $startno = $objNavi->start_row;

                // 取得範囲の指定(開始行番号、行数のセット)
                $objQuery->setlimitoffset(SEARCH_PMAX, $startno);
                // 表示順序
                $objQuery->setorder("customer_id DESC");

                // 検索結果の取得
                $col = $objSelect->getMailMagazineColumn($this->lfGetIsMobile($_POST['mail_type']));
                $this->arrResults = $objQuery->select($col, $from, $where, $arrval);
                //現在時刻の取得
                $this->arrNowDate = $this->lfGetNowDate();
            }
            break;
            /*
             input:検索結果画面「htmlmail内容設定」ボタン
            */
        case 'input':
            //-- 入力値コンバート
            $this->list_data = $this->lfConvertParam($_POST, $arrSearchColumn);
            //-- 入力エラーのチェック
            $this->arrErr = $this->lfErrorCheck($this->list_data);
            //-- エラーなし
            if (!is_array($this->arrErr)) {
                //-- 現在時刻の取得
                $this->arrNowDate = $this-lfGetNowDate();
                $this->arrHidden = $this->lfGetHidden($this->list_data); // hidden要素作成
                $this->tpl_mainpage = 'mail/input.tpl';
            }
            break;
            /*
             template:テンプレート選択
            */
        case 'template':
            //-- 入力値コンバート
            $this->list_data = $this->lfConvertParam($_POST, $arrSearchColumn);

            //-- 時刻設定の取得
            $this->arrNowDate['year'] = $_POST['send_year'];
            $this->arrNowDate['month'] = $_POST['send_month'];
            $this->arrNowDate['day'] = $_POST['send_day'];
            $this->arrNowDate['hour'] = $_POST['send_hour'];
            $this->arrNowDate['minutes'] = $_POST['send_minutes'];

            //-- 入力エラーのチェック
            $this->arrErr = $this->lfErrorCheck($this->list_data);

            //-- 検索開始
            if ( ! is_array($this->arrErr)) {
                $this->list_data['name'] = SC_Utils_Ex::sfManualEscape($this->list_data['name']);
                $this->arrHidden = $this->lfGetHidden($this->list_data); // hidden要素作成

                $this->tpl_mainpage = 'mail/input.tpl';
                $template_data = $this->getTemplateData($conn, $_POST['template_id']);
                if ( $template_data ){
                    foreach( $template_data as $key=>$val ){
                        $this->list_data[$key] = $val;
                    }
                }

                //-- HTMLテンプレートを使用する場合は、HTMLソースを生成してBODYへ挿入
                if ( $this->list_data["mail_method"] == 3) {
                    $objTemplate = new LC_HTMLtemplate;
                    $objTemplate->list_data = lfGetHtmlTemplateData($_POST['template_id']);
                    $objSiteInfo = new SC_SiteInfo();
                    $objTemplate->arrInfo = $objSiteInfo->data;
                    //メール担当写真の表示
                    $objUpFile = new SC_UploadFile(IMAGE_TEMP_URL, IMAGE_SAVE_URL);
                    $objUpFile->addFile("メール担当写真", 'charge_image', array('jpg'), IMAGE_SIZE, true, SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
                    $objUpFile->setDBFileList($objTemplate->list_data);
                    $objTemplate->arrFile = $objUpFile->getFormFileList(IMAGE_TEMP_URL, IMAGE_SAVE_URL);
                    $objMakeTemplate = new SC_AdminView();
                    $objMakeTemplate->assignobj($objTemplate);
                    $this->list_data["body"] = $objMakeTemplate->fetch("mail/html_template.tpl");
                }
            }
            break;
            /*
             regist_confirm:「入力内容を確認」
             regist_back:「テンプレート設定画面へ戻る」
             regist_complete:「登録」
            */
        case 'regist_confirm':
        case 'regist_back':
        case 'regist_complete':
            //-- 入力値コンバート
            $arrCheckColumn = array_merge( $arrSearchColumn, $arrRegistColumn );
            $this->list_data = $this->lfConvertParam($_POST, $arrCheckColumn);

            //現在時刻の取得
            $this->arrNowDate = $this->lfGetNowDate();

            //-- 入力エラーのチェック
            $this->arrErr = $this->lfErrorCheck($this->list_data, 1);
            $this->tpl_mainpage = 'mail/input.tpl';
            $this->arrHidden = $this->lfGetHidden($this->list_data); // hidden要素作成

            //-- 検索開始
            if ( ! is_array($this->arrErr)) {
                $this->list_data['name'] = SC_Utils_Ex::sfManualEscape($this->list_data['name']);
                if ( $_POST['mode'] == 'regist_confirm'){
                    $this->tpl_mainpage = 'mail/input_confirm.tpl';
                } else if( $_POST['mode'] == 'regist_complete' ){
                    $this->lfRegistData($conn, $this->list_data);
                    if(MELMAGA_SEND == true) {
                        if(MELMAGA_BATCH_MODE) {
                            $this->sendRedirect($this->getLocation(URL_DIR . "admin/mail/history.php"));
                        } else {
                            $this->sendRedirect($this->getLocation(URL_DIR . "admin/mail/sendmail.php", array("mode" => "now")));
                        }
                        exit;
                    } else {
                        SC_Utils_Ex::sfErrorHeader(">> 本サイトではメルマガ配信は行えません。");
                    }
                }
            }
            break;
        default:
            $this->list_data['mail_type'] = 1;
            break;
        }

        // 配信時間の年を、「現在年~現在年＋１」の範囲に設定
        for ($year=date("Y"); $year<=date("Y") + 1;$year++){
            $arrYear[$year] = $year;
        }
        $this->arrYear = $arrYear;

        $this->arrCustomerOrderId = $this->lfGetCustomerOrderId($_POST['buy_product_code']);

        $this->arrCatList = $objDb->sfGetCategoryList();

        $this->arrCampaignList = $this->lfGetCampaignList($objQuery);

        //---- ページ表示
        $objView->assignobj($this);
        $objView->display(MAIN_FRAME);

    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    // 商品コードで検索された場合にヒットした受注番号を取得する。
    function lfGetCustomerOrderId($keyword) {
        $arrCustomerOrderId = null;
        if($keyword != "") {
            $col = "dtb_order.customer_id, dtb_order.order_id";
            $from = "dtb_order LEFT JOIN dtb_order_detail USING(order_id)";
            $where = "product_code LIKE ? AND del_flg = 0";
            $val = SC_Utils_Ex::sfManualEscape($keyword);
            $arrVal[] = "%$val%";
            $objQuery = new SC_Query();
            $objQuery->setgroupby("customer_id, order_id");
            $arrRet = $objQuery->select($col, $from, $where, $arrVal);
            $arrCustomerOrderId = SC_Utils_Ex::sfArrKeyValues($arrRet, "customer_id", "order_id");
        }
        return $arrCustomerOrderId;
    }

    function lfMakeCsvData(&$conn, $send_id){
        $arrTitle  = array(  'name01','email');

        $sql = "SELECT name01,email FROM dtb_send_customer WHERE send_id = ? ORDER BY email";
        $result = $conn->getAll($sql, array($send_id) );

        if ( $result ){
            $return = $this->lfGetCSVData( $result, $arrTitle);
        }
        return $return;
    }

    //---- CSV出力用データ取得
    function lfGetCSVData( $array, $arrayIndex){

        for ($i=0; $i<count($array); $i++){

            for ($j=0; $j<count($array[$i]); $j++ ){
                if ( $j > 0 ) $return .= ",";
                $return .= "\"";
                if ( $arrayIndex ){
                    $return .= mb_ereg_replace("<","＜",mb_ereg_replace( "\"","\"\"",$array[$i][$arrayIndex[$j]] )) ."\"";
                } else {
                    $return .= mb_ereg_replace("<","＜",mb_ereg_replace( "\"","\"\"",$array[$i][$j] )) ."\"";
                }
            }
            $return .= "\n";
        }
        return $return;
    }

    //現在時刻の取得（配信時間デフォルト値）
    function lfGetNowDate(){
        $nowdate = date("Y/n/j/G/i");
        list($year, $month, $day, $hour, $minute) = split("[/]", $nowdate);
        $arrNowDate = array( 'year' => $year, 'month' => $month, 'day' => $day, 'hour' => $hour, 'minutes' => $minute);
        foreach ($arrNowDate as $key => $val){
            switch ($key){
            case 'minutes':
                $val = ereg_replace('^[0]','', $val);
                if ($val < 30){
                    $list_date[$key] = '30';
                }else{
                    $list_date[$key] = '00';
                }
                break;
            case 'year':
            case 'month':
            case 'day':
                $list_date[$key] = $val;
                break;
            }
        }
        if ($arrNowDate['minutes'] < 30){
            $list_date['hour'] = $hour;
        }else{
            $list_date['hour'] = $hour + 1;
        }
        return $list_date;
    }

    // 配信内容と配信リストを書き込む
    function lfRegistData(&$conn, $arrData){

        $objQuery = new SC_Query();
        $objSelect = new SC_CustomerList( lfConvertParam($arrData, $this->arrSearchColumn), "magazine" );

        $search_data = $conn->getAll($objSelect->getListMailMagazine(lfGetIsMobile($_POST['mail_type'])), $objSelect->arrVal);
        $dataCnt = count($search_data);

        $dtb_send_history = array();
        if(DB_TYPE=='pgsql'){
            $dtb_send_history["send_id"] = $objQuery->nextval('dtb_send_history', 'send_id');
        }
        $dtb_send_history["mail_method"] = $arrData['mail_method'];
        $dtb_send_history["subject"] = $arrData['subject'];
        $dtb_send_history["body"] = $arrData['body'];
        if(MELMAGA_BATCH_MODE) {
            $dtb_send_history["start_date"] = $arrData['send_year'] ."/".$arrData['send_month']."/".$arrData['send_day']." ".$arrData['send_hour'].":".$arrData['send_minutes'];
        } else {
            $dtb_send_history["start_date"] = "now()";
        }
        $dtb_send_history["creator_id"] = $_SESSION['member_id'];
        $dtb_send_history["send_count"] = $dataCnt;
        $arrData['body'] = "";
        $dtb_send_history["search_data"] = serialize($arrData);
        $dtb_send_history["update_date"] = "now()";
        $dtb_send_history["create_date"] = "now()";
        $objQuery->insert("dtb_send_history", $dtb_send_history );
        if(DB_TYPE == "mysql"){
            $dtb_send_history["send_id"] = $objQuery->nextval('dtb_send_history','send_id');
        }
        if ( is_array( $search_data ) ){
            foreach( $search_data as $line ){
                $dtb_send_customer = array();
                $dtb_send_customer["customer_id"] = $line["customer_id"];
                $dtb_send_customer["send_id"] = $dtb_send_history["send_id"];
                $dtb_send_customer["email"] = $line["email"];

                $dtb_send_customer["name"] = $line["name01"] . " " . $line["name02"];

                $conn->autoExecute("dtb_send_customer", $dtb_send_customer );
            }
        }
    }

    // キャンペーン一覧
    function lfGetCampaignList(&$objQuery) {
        $arrCampaign = null;
        $sql = "SELECT campaign_id, campaign_name FROM dtb_campaign ORDER BY update_date DESC";
        $arrResult = $objQuery->getall($sql);

        foreach($arrResult as $arrVal) {
            $arrCampaign[$arrVal['campaign_id']] = $arrVal['campaign_name'];
        }

        return $arrCampaign;
    }

    function lfGetIsMobile($mail_type) {
        // 検索結果の取得
        $is_mobile = false;
        switch($mail_type) {
        case 1:
            $is_mobile = false;
            break;
        case 2:
            $is_mobile = true;
            break;
        default:
            $is_mobile = false;
            break;
        }

        return $is_mobile;
    }


    //---- HTMLテンプレートを使用する場合、データを取得する。
    function lfGetHtmlTemplateData($id) {

        global $conn;
        $sql = "SELECT * FROM dtb_mailmaga_template WHERE template_id = ?";
        $result = $conn->getAll($sql, array($id));
        $list_data = $result[0];

        // メイン商品の情報取得
        $sql = "SELECT name, main_image, point_rate, deliv_fee, price01_min, price01_max, price02_min, price02_max FROM vw_products_allclass AS allcls WHERE product_id = ?";
        $main = $conn->getAll($sql, array($list_data["main_product_id"]));
        $list_data["main"] = $main[0];

        // サブ商品の情報取得
        $sql = "SELECT product_id, name, main_list_image, price01_min, price01_max, price02_min, price02_max FROM vw_products_allclass AS allcls WHERE product_id = ?";
        $k = 0;
        $l = 0;
        for ($i = 1; $i <= 12; $i ++) {
            if ($l == 4) {
                $l = 0;
                $k ++;
            }
            $result = "";
            $j = sprintf("%02d", $i);
            if ($i > 0 && $i < 5 ) $k = 0;
            if ($i > 4 && $i < 9 ) $k = 1;
            if ($i > 8 && $i < 13 ) $k = 2;

            if (is_numeric($list_data["sub_product_id" .$j])) {
                $result = $conn->getAll($sql, array($list_data["sub_product_id" .$j]));
                $list_data["sub"][$k][$l] = $result[0];
                $list_data["sub"][$k]["data_exists"] = "OK";    //当該段にデータが１つ以上存在するフラグ
            }
            $l ++;
        }
        return $list_data;
    }

    //---   テンプレートの種類を返す
    function lfGetTemplateMethod($conn, $templata_id){

        if ( sfCheckNumLength($template_id) ){
            $sql = "SELECT mail_method FROM dtb_mailmaga_template WEHRE template_id = ?";
        }
    }

    //---   hidden要素出力用配列の作成
    function lfGetHidden( $array ){
        if ( is_array($array) ){
            foreach( $array as $key => $val ){
                if ( is_array( $val )){
                    for ( $i=0; $i<count($val); $i++){
                        $return[ $key.'['.$i.']'] = $val[$i];
                    }
                } else {
                    $return[$key] = $val;
                }
            }
        }
        return $return;
    }

    //----　取得文字列の変換
    function lfConvertParam($array, $arrSearchColumn) {

        // 文字変換
        foreach ($arrSearchColumn as $data) {
            $arrConvList[ $data["column"] ] = $data["convert"];
        }

        $new_array = array();
        foreach ($arrConvList as $key => $val) {
            if ( strlen($array[$key]) > 0 ){                        // データのあるものだけ返す
                $new_array[$key] = $array[$key];
                if( strlen($val) > 0) {
                    $new_array[$key] = mb_convert_kana($new_array[$key] ,$val);
                }
            }
        }
        return $new_array;

    }


    //---- 入力エラーチェック
    function lfErrorCheck($array, $flag = '') {

        // flag は登録時用

        $objErr = new SC_CheckError($array);

        $objErr->doFunc(array("顧客コード", "customer_id", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("都道府県", "pref", 2), array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("顧客名", "name", STEXT_LEN), array("MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("顧客名(カナ)", "kana", STEXT_LEN), array("KANA_CHECK", "MAX_LENGTH_CHECK"));

        $objErr->doFunc(array('メールアドレス', "email", STEXT_LEN) ,array("EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("電話番号", "tel", TEL_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));

        $objErr->doFunc(array("購入回数(開始)", "buy_times_from", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("購入回数(終了)", "buy_times_to", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
        if ((is_numeric($array["buy_total_from"]) && is_numeric($array["buy_total_to"]) ) && ($array["buy_times_from"] > $array["buy_times_to"]) ) $objErr->arrErr["buy_times_from"] .= "※ 購入回数の指定範囲が不正です。";

        $objErr->doFunc(array("誕生月", "birth_month", 2), array("NUM_CHECK","MAX_LENGTH_CHECK"));

        $objErr->doFunc(array("誕生日(開始日)", "b_start_year", "b_start_month", "b_start_day",), array("CHECK_DATE"));
        $objErr->doFunc(array("誕生日(終了日)", "b_end_year", "b_end_month", "b_end_day"), array("CHECK_DATE"));
        $objErr->doFunc(array("誕生日(開始日)","誕生日(終了日)", "b_start_year", "b_start_month", "b_start_day", "b_end_year", "b_end_month", "b_end_day"), array("CHECK_SET_TERM"));

        $objErr->doFunc(array("登録・更新日(開始日)", "start_year", "start_month", "start_day",), array("CHECK_DATE"));
        $objErr->doFunc(array("登録・更新日(終了日)", "end_year", "end_month", "end_day"), array("CHECK_DATE"));
        $objErr->doFunc(array("登録・更新日(開始日)","登録・更新日(終了日)", "start_year", "start_month", "start_day", "end_year", "end_month", "end_day"), array("CHECK_SET_TERM"));

        $objErr->doFunc(array("最終購入日(開始日)", "buy_start_year", "buy_start_month", "buy_start_day",), array("CHECK_DATE"));
        $objErr->doFunc(array("最終購入(終了日)", "buy_end_year", "buy_end_month", "buy_end_day"), array("CHECK_DATE"));
        $objErr->doFunc(array("最終購入日(開始日)","登録・更新日(終了日)", "buy_start_year", "buy_start_month", "buy_start_day", "buy_end_year", "buy_end_month", "buy_end_day"), array("CHECK_SET_TERM"));

        $objErr->doFunc(array("購入商品コード", "buy_product_code", STEXT_LEN), array("MAX_LENGTH_CHECK"));

        $objErr->doFunc(array("購入商品名", "buy_product_name", STEXT_LEN), array("MAX_LENGTH_CHECK"));

        $objErr->doFunc(array("購入金額(開始)", "buy_total_from", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("購入金額(終了)", "buy_total_to", INT_LEN), array("NUM_CHECK","MAX_LENGTH_CHECK"));

        $objErr->doFunc(array("キャンペーン", "campaign_id", INT_LEN), array("NUM_CHECK"));

        //購入金額(from) ＞ 購入金額(to) の場合はエラーとする
        if ( (is_numeric($array["buy_total_from"]) && is_numeric($array["buy_total_to"]) ) &&
             ($array["buy_total_from"] > $array["buy_total_to"]) ) {
            $objErr->arrErr["buy_total_from"] .= "※ 購入金額の指定範囲が不正です。";
        }

        if ( $flag ){
            $objErr->doFunc(array("テンプレート", "template_id"), array("EXIST_CHECK", "NUM_CHECK"));
            $objErr->doFunc(array("メール送信法法", "mail_method"), array("EXIST_CHECK", "NUM_CHECK"));

            if(MELMAGA_BATCH_MODE) {
                $objErr->doFunc(array("配信日（年）","send_year"), array("EXIST_CHECK", "NUM_CHECK"));
                $objErr->doFunc(array("配信日（月）","send_month"), array("EXIST_CHECK", "NUM_CHECK"));
                $objErr->doFunc(array("配信日（日）","send_day"), array("EXIST_CHECK", "NUM_CHECK"));
                $objErr->doFunc(array("配信日（時）","send_hour"), array("EXIST_CHECK", "NUM_CHECK"));
                $objErr->doFunc(array("配信日（分）","send_minutes"), array("EXIST_CHECK", "NUM_CHECK"));
                $objErr->doFunc(array("配信日", "send_year", "send_month", "send_day"), array("CHECK_DATE"));
                $objErr->doFunc(array("配信日", "send_year", "send_month", "send_day","send_hour", "send_minutes"), array("ALL_EXIST_CHECK"));
            }
            $objErr->doFunc(array("Subject", "subject", STEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
            $objErr->doFunc(array("本文", 'body', LLTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));    // HTMLテンプレートを使用しない場合
        }

        return $objErr->arrErr;
    }

    /* テンプレートIDとsubjectの配列を返す */
    function getTemplateList($conn){
        $return = "";
        $sql = "SELECT template_id, subject, mail_method FROM dtb_mailmaga_template WHERE del_flg = 0 ";
        if ($_POST["htmlmail"] == 2 || $_POST['mail_type'] == 2) {
            $sql .= " AND mail_method = 2 ";    //TEXT希望者へのTESTメールテンプレートリスト
        }
        $sql .= " ORDER BY template_id DESC";
        $result = $conn->getAll($sql);

        if ( is_array($result) ){
            foreach( $result as $line ){
                $return[$line['template_id']] = "【" . $this->arrMagazineTypeAll[$line['mail_method']] . "】" . $line['subject'];
            }
        }

        return $return;
    }

    /* テンプレートIDからテンプレートデータを取得 */
    function getTemplateData($conn, $id){

        if ( sfCheckNumLength($id) ){
            $sql = "SELECT * FROM dtb_mailmaga_template WHERE template_id = ? ORDER BY template_id DESC";
            $result = $conn->getAll( $sql, array($id) );
            if ( is_array($result) ) {
                $return = $result[0];
            }
        }
        return $return;
    }

}
class LC_HTMLtemplate {
    var $list_data;
}

?>
