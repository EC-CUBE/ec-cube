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
 * メルマガ管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Mail extends LC_Page_Admin {

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
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_subtitle = '配信内容設定';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData('mtb_pref');
        $this->arrJob = $masterData->getMasterData("mtb_job");
        $this->arrJob["不明"] = "不明";
        $this->arrSex = $masterData->getMasterData("mtb_sex");
        $this->arrPageRows = $masterData->getMasterData("mtb_page_rows");
        $this->arrHtmlmail = array( "" => "両方",  1 => "HTML", 2 => "TEXT" );
        $this->arrMailType = $masterData->getMasterData("mtb_mail_type");
        
        // 日付プルダウン設定
        $objDate = new SC_Date(BIRTH_YEAR);
        $this->arrYear = $objDate->getYear();   
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();
        $this->objDate = $objDate;

        // カテゴリ一覧設定
        $objDb = new SC_Helper_DB_Ex();
        $this->arrCatList = $objDb->sfGetCategoryList();
        
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
        
         // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess(new SC_Session());

        // パラメータ管理クラス
        $objFormParam = new SC_FormParam();
        $this->lfInitParamSearchCustomer($objFormParam);
        $objFormParam->setParam($_POST);

        // パラメーター読み込み
        $this->arrHidden = $objFormParam->getSearchArray();
        $this->arrForm = $objFormParam->getFormParamList();

        // 入力パラメーターチェック
        $this->arrErr = $this->lfCheckError($objFormParam);
        if(!SC_Utils_Ex::isBlank($this->arrErr)) {
            return;
        }

        // モードによる処理切り替え
        switch ($this->getMode()) {
        case 'search':
            list($this->tpl_linemax, $this->arrResults, $this->objNavi) = $this->lfDoSearch($objFormParam->getHashArray());
            $this->arrPagenavi = $this->objNavi->arrPagenavi;
            break;
        default:
            break;
        }
        
        
        /*
        // ページ初期設定
        $objDate = new SC_Date();
        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();
        $this->objDate = $objDate;
        $this->arrTemplate = $this->getTemplateList($objQuery);

        $objSess = new SC_Session();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        switch($this->getMode()) {
        case 'query':
            // query:配信履歴「確認」
            if (SC_Utils_Ex::sfIsInt($_GET["send_id"])) {
                // 送信履歴より、送信条件確認画面
                $sql = "SELECT search_data FROM dtb_send_history WHERE send_id = ?";
                $result = $objQuery->getOne($sql, array($_GET["send_id"]));
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
                $this->setTemplate('mail/query.tpl');
                return;
            }
            break;
             //search:「検索」ボタン
             //back:検索結果画面「戻る」ボタン
        case 'search':
        case 'back':
            // 入力値コンバート
            $this->list_data = $this->lfConvertParam($_POST, $this->arrSearchColumn);

            // 入力エラーのチェック
            $this->arrErr = $this->lfErrorCheck($this->list_data);

            // 検索開始
            if (empty($this->arrErr)) {
                $this->list_data['name'] = isset($this->list_data['name'])
                    ? $this->list_data['name'] : "";
                // hidden要素作成
                $this->arrHidden = $this->lfGetHidden($this->list_data);

                // 検索データ取得
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
                $objNavi = new SC_PageNavi($this->tpl_pageno, $linemax, SEARCH_PMAX, "fnResultPageNavi", NAVI_PMAX);
                $this->arrPagenavi = $objNavi->arrPagenavi;
                $startno = $objNavi->start_row;

                // 取得範囲の指定(開始行番号、行数のセット)
                $objQuery->setLimitOffset(SEARCH_PMAX, $startno);
                // 表示順序
                $objQuery->setOrder("customer_id DESC");

                // 検索結果の取得
                $col = $objSelect->getMailMagazineColumn($this->lfGetIsMobile($_POST['mail_type']));
                $this->arrResults = $objQuery->select($col, $from, $where, $arrval);
                // 現在時刻の取得
                $this->arrNowDate = $this->lfGetNowDate();
            }
            break;
             // input:検索結果画面「htmlmail内容設定」ボタン
        case 'input':
            // 入力値コンバート
            $this->list_data = $this->lfConvertParam($_POST, $this->arrSearchColumn);
            // 入力エラーのチェック
            $this->arrErr = $this->lfErrorCheck($this->list_data);
            // エラーなし
            if (empty($this->arrErr)) {
                // 現在時刻の取得
                $this->arrNowDate = $this->lfGetNowDate();
                $this->arrHidden = $this->lfGetHidden($this->list_data); // hidden要素作成
                $this->tpl_mainpage = 'mail/input.tpl';
            }
            break;
            // template:テンプレート選択
        case 'template':
            // 入力値コンバート
            $this->list_data = $this->lfConvertParam($_POST, $this->arrSearchColumn);

            // 時刻設定の取得
            $this->arrNowDate['year'] = isset($_POST['send_year']) ? $_POST['send_year'] : "";
            $this->arrNowDate['month'] = isset($_POST['send_month']) ? $_POST['send_month'] : "";
            $this->arrNowDate['day'] = isset($_POST['send_day']) ? $_POST['send_day'] : "";
            $this->arrNowDate['hour'] = isset($_POST['send_hour']) ? $_POST['send_hour'] : "";
            $this->arrNowDate['minutes'] = isset($_POST['send_minutes']) ? $_POST['send_minutes'] : "";

            // 入力エラーのチェック
            $this->arrErr = $this->lfErrorCheck($this->list_data);

            // 検索開始
            if (empty($this->arrErr)) {
                $this->list_data['name'] = isset($this->list_data['name']) ? $this->list_data['name'] : "";
                $this->arrHidden = $this->lfGetHidden($this->list_data); // hidden要素作成

                $this->tpl_mainpage = 'mail/input.tpl';
                $template_data = $this->getTemplateData($objQuery, $_POST['template_id']);
                if ( $template_data ){
                    foreach( $template_data as $key=>$val ){
                        $this->list_data[$key] = $val;
                    }
                }

            }
            break;
           //  regist_confirm:「入力内容を確認」
           //  regist_back:「テンプレート設定画面へ戻る」
           //  regist_complete:「登録」
        case 'regist_confirm':
        case 'regist_back':
        case 'regist_complete':
            // 入力値コンバート
            $this->arrCheckColumn = array_merge( $this->arrSearchColumn, $this->arrRegistColumn );
            $this->list_data = $this->lfConvertParam($_POST, $this->arrCheckColumn);

            // 現在時刻の取得
            $this->arrNowDate = $this->lfGetNowDate();

            // 入力エラーのチェック
            $this->arrErr = $this->lfErrorCheck($this->list_data, 1);
            $this->tpl_mainpage = 'mail/input.tpl';
            $this->arrHidden = $this->lfGetHidden($this->list_data); // hidden要素作成

            // 検索開始
            if (empty($this->arrErr)) {
                $this->list_data['name'] =
                    isset($this->list_data['name'])
                    ? $this->list_data['name'] : "";
                //TODO 要リファクタリング(MODE if利用)
                if ( $this->getMode() == 'regist_confirm'){
                    $this->tpl_mainpage = 'mail/input_confirm.tpl';
                } else if( $this->getMode() == 'regist_complete' ){
                    $sendId = $this->lfRegistData($objQuery, $this->list_data);
                    SC_Response_Ex::sendRedirectFromUrlPath(ADMIN_DIR . 'mail/sendmail.php', array('mode' => 'now', 'send_id' => $sendId));
                    exit;
                }
            }
            break;
        default:
        }
        */
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
     * パラメーター情報の初期化
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return void
     */
    function lfInitParamSearchCustomer(&$objFormParam) {
        SC_Helper_Customer_Ex::sfSetSearchParam($objFormParam);
        $objFormParam->addParam('配信形式', 'search_htmlmail', INT_LEN, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('配信メールアドレス種別', 'search_mail_type', INT_LEN, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"));
        $objFormParam->addParam('ページ番号', 'search_pageno', INT_LEN, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"),1,false);
        $objFormParam->addParam('１ページ表示件数', 'search_page_rows', INT_LEN, 'n', array("NUM_CHECK","MAX_LENGTH_CHECK"),1,false);
    }
    
    /**
     * エラーチェック
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return array エラー配列
     */
    function lfCheckError(&$objFormParam) {
        return SC_Helper_Customer_Ex::sfCheckErrorSearchParam($objFormParam);
    }
    
    /**
     * 顧客一覧を検索する処理
     *
     * @param array $arrParam 検索パラメーター連想配列
     * @return array( integer 全体件数, mixed 顧客データ一覧配列, mixed SC_PageNaviオブジェクト)
     */
    function lfDoSearch($arrParam) {
        $objQuery =& SC_Query::getSingletonInstance();
        $objSelect = new SC_CustomerList($arrParam, "customer");
        $page_rows = $arrParam['search_page_rows'];
        if(SC_Utils_Ex::sfIsInt($page_rows)) {
            $page_max = $page_rows;
        }else{
            $page_max = SEARCH_PMAX;
        }
        $disp_pageno = $arrParam['search_pageno'];
        if($disp_pageno == 0) {
            $disp_pageno = 1;
        }
        $offset = intval($page_max) * (intval($disp_pageno) - 1);
        $objSelect->setLimitOffset($page_max, $offset);
        
        $arrData = $objQuery->getAll($objSelect->getList(), $objSelect->arrVal);

        // 該当全体件数の取得
        $linemax = $objQuery->getOne($objSelect->getListCount(), $objSelect->arrVal);

        // ページ送りの取得
        $objNavi = new SC_PageNavi($arrParam['search_pageno'],
                                    $linemax,
                                    $page_max,
                                    "fnCustomerPage",
                                    NAVI_PMAX);
        return array($linemax, $arrData, $objNavi);
    }
    
    // 現在時刻の取得（配信時間デフォルト値）
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

    /**
     * 配信内容と配信リストを書き込む
     *
     * @return string 登録した行の dtb_send_history.send_id の値
     */
    function lfRegistData(&$objQuery, $arrData){

        $objSelect = new SC_CustomerList($this->lfConvertParam($arrData, $this->arrSearchColumn), "magazine" );

        $search_data = $objQuery->getAll($objSelect->getListMailMagazine($this->lfGetIsMobile($_POST['mail_type'])), $objSelect->arrVal);
        $dataCnt = count($search_data);

        $dtb_send_history = array();
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
        $dtb_send_history['send_id'] = $objQuery->nextVal('dtb_send_history_send_id');
        $objQuery->insert("dtb_send_history", $dtb_send_history );

        $sendId = $objQuery->currval('dtb_send_history_send_id');

        if ( is_array( $search_data ) ){
            foreach( $search_data as $line ){
                $dtb_send_customer = array();
                $dtb_send_customer["customer_id"] = $line["customer_id"];
                $dtb_send_customer["send_id"] = $sendId;
                $dtb_send_customer["email"] = $line["email"];
                $dtb_send_customer["name"] = $line["name01"] . " " . $line["name02"];
                $objQuery->insert("dtb_send_customer", $dtb_send_customer );
            }
        }

        return $sendId;
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

    // hidden要素出力用配列の作成
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

    // 取得文字列の変換
    function lfConvertParam($array, $arrSearchColumn) {

        // 文字変換
        foreach ($arrSearchColumn as $data) {
            $arrConvList[ $data["column"] ] = $data["convert"];
        }

        $new_array = array();
        foreach ($arrConvList as $key => $val) {
            if (isset($array[$key]) &&  strlen($array[$key]) > 0 ){                        // データのあるものだけ返す
                $new_array[$key] = $array[$key];
                if( strlen($val) > 0) {
                    $new_array[$key] = mb_convert_kana($new_array[$key] ,$val);
                }
            }
        }
        return $new_array;

    }


    // 入力エラーチェック
    function lfErrorCheck($array, $flag = '') {

        // flag は登録時用

        $objErr = new SC_CheckError($array);

        if ( $flag ){
            $objErr->doFunc(array("テンプレート", "template_id"), array("EXIST_CHECK", "NUM_CHECK"));
            $objErr->doFunc(array("メール送信法法", "mail_method"), array("EXIST_CHECK", "NUM_CHECK"));
            $objErr->doFunc(array("Subject", "subject", STEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
            $objErr->doFunc(array("本文", 'body', LLTEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));    // HTMLテンプレートを使用しない場合
        }

        return $objErr->arrErr;
    }

    /* テンプレートIDとsubjectの配列を返す */
    function getTemplateList(&$objQuery){
        $return = "";
        $sql = "SELECT template_id, subject, mail_method FROM dtb_mailmaga_template WHERE del_flg = 0 ";
        if ($_POST["htmlmail"] == 2 || $_POST['mail_type'] == 2) {
            $sql .= " AND mail_method = 2 ";    // TEXT希望者へのTESTメールテンプレートリスト
        }
        $sql .= " ORDER BY template_id DESC";
        $result = $objQuery->getAll($sql);

        if ( is_array($result) ){
            foreach( $result as $line ){
                $return[$line['template_id']] = "【" . $this->arrMagazineTypeAll[$line['mail_method']] . "】" . $line['subject'];
            }
        }

        return $return;
    }

    /* テンプレートIDからテンプレートデータを取得 */
    function getTemplateData(&$objQuery, $id){

        if ( SC_Utils_Ex::sfIsInt($id) ){
            $sql = "SELECT * FROM dtb_mailmaga_template WHERE template_id = ? ORDER BY template_id DESC";
            $result = $objQuery->getAll( $sql, array($id) );
            if ( is_array($result) ) {
                $return = $result[0];
            }
        }
        return $return;
    }

}
?>
