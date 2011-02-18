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
require_once(CLASS_EX_REALDIR . "helper_extends/SC_Helper_CSV_Ex.php");

/**
 * レビュー管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Products_Review extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/review.tpl';
        $this->tpl_subnavi = 'products/subnavi.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subno = 'review';
        $this->tpl_pager = 'pager.tpl';
        $this->tpl_subtitle = 'レビュー管理';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPageMax = $masterData->getMasterData("mtb_page_max");
        $this->arrRECOMMEND = $masterData->getMasterData("mtb_recommend");
        $this->arrSex = $masterData->getMasterData("mtb_sex");
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
        $objSess = new SC_Session();
        $objDate = new SC_Date();
        $objQuery =& SC_Query::getSingletonInstance();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        // 登録・更新検索開始年
        $objDate->setStartYear(RELEASE_YEAR);
        $objDate->setEndYear(DATE("Y"));
        $this->arrStartYear = $objDate->getYear();
        $this->arrStartMonth = $objDate->getMonth();
        $this->arrStartDay = $objDate->getDay();
        // 登録・更新検索終了年
        $objDate->setStartYear(RELEASE_YEAR);
        $objDate->setEndYear(DATE("Y"));
        $this->arrEndYear = $objDate->getYear();
        $this->arrEndMonth = $objDate->getMonth();
        $this->arrEndDay = $objDate->getDay();

        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam();
        $this->objFormParam->setParam($_POST);
        $arrForm = $this->objFormParam->getHashArray();

        // hidden の設定
        $this->arrHidden = $this->lfSetHidden($arrForm);

        switch ($this->getMode()) {
        case 'delete':
            $this->lfDeleteReview($arrForm['review_id']);
        case 'search':
        case 'csv':
            // エラーチェック
            $this->arrErr = $this->lfCheckError();
            if (!$this->arrErr){
                // 検索条件を取得
                list($where, $arrval) = $this->lfGetWhere($arrForm);
            }

            //CSVダウンロード
            if ($this->getMode() == 'csv') {
                $this->lfCsv($where, $arrval);
                exit;
            }

            // 検索条件を取得
            $this->arrReview = $this->lfGetRevire($arrForm, $where, $arrval);
            break;
        default:
            break;
        }

        $this->arrForm = $arrForm;
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    // 入力エラーチェック
    function lfCheckError() {
        // 入力データを渡す。
        $arrRet =  $this->objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError();

        switch ($this->getMode()){
        case 'search':
            $objErr->doFunc(array("投稿者", "search_startyear", "search_startmonth", "search_startday"), array("CHECK_DATE"));
            $objErr->doFunc(array("開始日", "search_startyear", "search_startmonth", "search_startday"), array("CHECK_DATE"));
            $objErr->doFunc(array("終了日", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_DATE"));
            $objErr->doFunc(array("開始日", "終了日", "search_startyear", "search_startmonth", "search_startday", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_SET_TERM"));
            break;

        case 'complete':
            $objErr->doFunc(array("おすすめレベル", "recommend_level"), array("SELECT_CHECK"));
            $objErr->doFunc(array("タイトル", "title", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
            $objErr->doFunc(array("コメント", "comment", LTEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
            break;
        }
        return $objErr->arrErr;
    }

    //レビューの削除
    function lfDeleteReview($review_id) {
        $objQuery =& SC_Query::getSingletonInstance();
        $sqlval['del_flg'] = 1;
        $objQuery->update("dtb_review", $sqlval, "review_id = ?", array($review_id));
    }

    // 検索ワードの引き継ぎ
    function lfSetHidden($arrForm) {
        $arrHidden = array();
        foreach ($arrForm AS $key=>$val) {
            if (preg_match("/^search_/", $key)) {
                switch ($key){
                case 'search_sex':
                    $arrHidden[$key] = SC_Utils_Ex::sfMergeParamCheckBoxes($val);
                    if(!is_array($val)) {
                        $arrForm[$key] = split("-", $val);
                    }
                    break;

                default:
                    $arrHidden[$key] = $val;
                    break;
                }
            }
        }
        return $arrHidden;
    }

    /* パラメータ情報の初期化 */
    function lfInitParam() {
        $this->objFormParam->addParam("投稿者名", "search_reviewer_name", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("投稿者URL", "search_reviewer_url", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("商品名", "search_name", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("商品コード", "search_product_code", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("性別", "search_sex", INT_LEN, "n", array("MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("おすすめレベル", "search_recommend_level", INT_LEN, "n", array("MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("投稿年", "search_startyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("投稿月", "search_startmonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("投稿日", "search_startday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("投稿年", "search_endyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("投稿月", "search_endmonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("投稿日", "search_endday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("最大表示件数", "search_page_max", INT_LEN, "n", array("MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("ページ番号件数", "search_pageno", INT_LEN, "n", array("MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("レビューID", "review_id", INT_LEN, "n", array("MAX_LENGTH_CHECK"));
    }

    // CSV ファイル出力実行
    function lfCsv($where, $arrval) {
        $objCSV = new SC_Helper_CSV_Ex();
        // CSV出力タイトル行の作成
        $head = SC_Utils_Ex::sfGetCSVList($objCSV->arrREVIEW_CVSTITLE);
        $data = $objCSV->lfGetReviewCSV($where, '', $arrval);
        // CSVを送信する。
        list($fime_name, $data) = SC_Utils_Ex::sfGetCSVData($head.$data);
        $this->sendResponseCSV($fime_name, $data);
    }


    // 検索条件の取得
    function lfGetWhere($arrForm) {
        //削除されていない商品を検索
        $where = "A.del_flg = 0 AND B.del_flg = 0";

        foreach ($arrForm AS $key=>$val){
            if (empty($val)) continue;

            switch ($key){
            case 'search_reviewer_name':
                $val = mb_convert_kana($val, 's');
                $val = preg_replace("/ /", "%", $val);
                $where.= " AND reviewer_name ILIKE ? ";
                $arrval[] = "%$val%";
                break;

            case 'search_reviewer_url':
                $val = mb_convert_kana($val, 's');
                $val = preg_replace("/ /", "%", $val);
                $where.= " AND reviewer_url ILIKE ? ";
                $arrval[] = "%$val%";
                break;

            case 'search_name':
                $val = mb_convert_kana($val, 's');
                $val = preg_replace("/ /", "%", $val);
                $where.= " AND name ILIKE ? ";
                $arrval[] = "%$val%";
                break;

            case 'search_product_code':
                $val = mb_convert_kana($val, 's');
                $val = preg_replace("/ /", "%", $val);
                $where.= " AND A.product_id IN (SELECT product_id FROM dtb_products_class WHERE product_code ILIKE ? )";
                $arrval[] = "%$val%";
                break;

            case 'search_sex':
                $tmp_where = "";
                //$val=配列の中身,$element=各キーの値(1,2)
                if (is_array($val)){
                    foreach($val as $element) {
                        if($element != "") {
                            if($tmp_where == "") {
                                $tmp_where .= " AND (sex = ?";
                            } else {
                                $tmp_where .= " OR sex = ?";
                            }
                            $arrval[] = $element;
                        }
                    }
                    if($tmp_where != "") {
                        $tmp_where .= ")";
                        $where .= " $tmp_where ";
                    }
                }

                break;

            case 'search_recommend_level':
                $where.= " AND recommend_level = ? ";
                $arrval[] = $val;
                break;

            case 'search_startyear':
                if (isset($_POST['search_startyear']) && isset($_POST['search_startmonth']) && isset($_POST['search_startday'])){
                    $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_startyear'], $_POST['search_startmonth'], $_POST['search_startday']);
                    $where.= " AND A.create_date >= ? ";
                    $arrval[] = $date;
                }
                break;

            case 'search_endyear':
                if (isset($_POST['search_startyear']) && isset($_POST['search_startmonth']) && isset($_POST['search_startday'])){
                    $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_endyear'], $_POST['search_endmonth'], $_POST['search_endday']);
                    $end_date = date("Y/m/d",strtotime("1 day" ,strtotime($date)));
                    $where.= " AND A.create_date <= cast('$end_date' as date) ";
                }
                break;
            }

        }
        return array($where, $arrval);
    }

    /*
     * レビューの検索結果取得
     */
    function lfGetRevire($arrForm, $where, $arrval) {
        $objQuery =& SC_Query::getSingletonInstance();

        // ページ送りの処理
        if(is_numeric($arrForm['search_page_max'])) {
            $page_max = $arrForm['search_page_max'];
        } else {
            $page_max = SEARCH_PMAX;
        }

        if (!isset($arrval)) $arrval = array();

        $from = "dtb_review AS A LEFT JOIN dtb_products AS B ON A.product_id = B.product_id ";
        $linemax = $objQuery->count($from, $where, $arrval);
        $this->tpl_linemax = $linemax;

        $this->tpl_pageno = isset($arrForm['search_pageno']) ? $arrForm['search_pageno'] : "";

        // ページ送りの取得
        $objNavi = new SC_PageNavi($this->tpl_pageno, $linemax, $page_max,
                                   "fnNaviSearchPage", NAVI_PMAX);
        $this->arrPagenavi = $objNavi->arrPagenavi;
        $startno = $objNavi->start_row;

        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setLimitOffset($page_max, $startno);

        // 表示順序
        $order = "A.create_date DESC";
        $objQuery->setOrder($order);
        //検索結果の取得
        //レビュー情報のカラムの取得
        $col = "review_id, A.product_id, reviewer_name, sex, recommend_level, ";
        $col .= "reviewer_url, title, comment, A.status, A.create_date, A.update_date, name";
        $from = "dtb_review AS A LEFT JOIN dtb_products AS B ON A.product_id = B.product_id ";
        $arrReview = $objQuery->select($col, $from, $where, $arrval);

        return $arrReview;
    }


}
?>
