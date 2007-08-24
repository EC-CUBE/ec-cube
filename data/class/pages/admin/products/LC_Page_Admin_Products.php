<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 商品管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Products extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/index.tpl';
        $this->tpl_mainno = 'products';
        $this->tpl_subnavi = 'products/subnavi.tpl';
        $this->tpl_subno = 'index';
        $this->tpl_pager = DATA_PATH . 'Smarty/templates/admin/pager.tpl';
        $this->tpl_subtitle = '商品マスタ';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPageMax = $masterData->getMasterData("mtb_page_max");
        $this->arrDISP = $masterData->getMasterData("mtb_disp");
        $this->arrSTATUS = $masterData->getMasterData("mtb_status");
        $this->arrPRODUCTSTATUS_COLOR = $masterData->getMasterData("mtb_product_status_color");

        $this->allowClientCache();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_AdminView();

        $objDate = new SC_Date();

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

        // 認証可否の判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        //キャンペーンの編集時
        if(isset($_POST['campaign_id']) && SC_Utils_Ex::sfIsInt($_POST['campaign_id'])
                && $_POST['mode'] == "camp_search") {
            $objQuery = new SC_Query();
            $search_data = $objQuery->get("dtb_campaign", "search_condition", "campaign_id = ? ", array($_POST['campaign_id']));
            $arrSearch = unserialize($search_data);
            foreach ($arrSearch as $key => $val) {
                $_POST[$key] = $val;
            }
        }

        // POST値の引き継ぎ
        $this->arrForm = $_POST;

        // 検索ワードの引き継ぎ
        foreach ($_POST as $key => $val) {
            if (ereg("^search_", $key) || ereg("^campaign_", $key)) {
                switch($key) {
                    case 'search_product_flag':
                    case 'search_status':
                        $this->arrHidden[$key] = sfMergeParamCheckBoxes($val);
                        if(!is_array($val)) {
                            $this->arrForm[$key] = split("-", $val);
                        }
                        break;
                    default:
                        $this->arrHidden[$key] = $val;
                        break;
                }
            }
        }

        // ページ送り用
        $this->arrHidden['search_pageno'] = isset($_POST['search_pageno']) ? $_POST['search_pageno'] : "";

        // 商品削除
        if ($_POST['mode'] == "delete") {

            if($_POST['category_id'] != "") {
                // ランク付きレコードの削除
                $where = "category_id = " . addslashes($_POST['category_id']);
                sfDeleteRankRecord("dtb_products", "product_id", $_POST['product_id'], $where);
            } else {
                sfDeleteRankRecord("dtb_products", "product_id", $_POST['product_id']);
            }
            // 子テーブル(商品規格)の削除
            $objQuery = new SC_Query();
            $objQuery->delete("dtb_products_class", "product_id = ?", array($_POST['product_id']));

            // 件数カウントバッチ実行
            SC_Utils_Ex::sfCategory_Count($objQuery);
        }


        if ($_POST['mode'] == "search" || $_POST['mode'] == "csv"  || $_POST['mode'] == "delete" || $_POST['mode'] == "delete_all" || $_POST['mode'] == "camp_search") {
            // 入力文字の強制変換
            $this->lfConvertParam();
            // エラーチェック
            $this->arrErr = $this->lfCheckError();

            $where = "del_flg = 0";
            $view_where = "del_flg = 0";

            // 入力エラーなし
            if (count($this->arrErr) == 0) {

                $arrval = array();
                foreach ($this->arrForm as $key => $val) {
                    $val = SC_Utils_Ex::sfManualEscape($val);

                    if($val == "") {
                        continue;
                    }

                    switch ($key) {
                        case 'search_product_id':	// 商品ID
                            $where .= " AND product_id = ?";
                            $view_where .= " AND product_id = ?";
                            $arrval[] = $val;
                            break;
                        case 'search_product_class_name': //規格名称
                            $where_in = " (SELECT classcategory_id FROM dtb_classcategory WHERE class_id IN (SELECT class_id FROM dtb_class WHERE name LIKE ?)) ";
                            $where .= " AND product_id IN (SELECT product_id FROM dtb_products_class WHERE classcategory_id1 IN " . $where_in;
                            $where .= " OR classcategory_id2 IN" . $where_in . ")";
                            $view_where .= " AND product_id IN (SELECT product_id FROM dtb_products_class WHERE classcategory_id1 IN " . $where_in;
                            $view_where .= " OR classcategory_id2 IN" . $where_in . ")";
                            $arrval[] = "%$val%";
                            $arrval[] = "%$val%";
                            $view_where = $where;
                            break;
                        case 'search_name':			// 商品名
                            $where .= " AND name ILIKE ?";
                            $view_where .= " AND name ILIKE ?";
                            $arrval[] = "%$val%";
                            break;
                        case 'search_category_id':	// カテゴリー
                            list($tmp_where, $tmp_arrval) = SC_Utils_Ex::sfGetCatWhere($val);
                            if($tmp_where != "") {
                                $where.= " AND $tmp_where";
                                $view_where.= " AND $tmp_where";
                                $arrval = array_merge((array)$arrval, (array)$tmp_arrval);
                            }
                            break;
                        case 'search_product_code':	// 商品コード
                            $where .= " AND product_id IN (SELECT product_id FROM dtb_products_class WHERE product_code ILIKE ? GROUP BY product_id)";
                            $view_where .= " AND EXISTS (SELECT product_id FROM dtb_products_class as cls WHERE cls.product_code ILIKE ? AND dtb_products.product_id = cls.product_id GROUP BY cls.product_id )";
                            $arrval[] = "%$val%";
                            break;
                        case 'search_startyear':	// 登録更新日（FROM）
                            $date = sfGetTimestamp($_POST['search_startyear'], $_POST['search_startmonth'], $_POST['search_startday']);
                            $where.= " AND update_date >= '" . $_POST['search_startyear'] . "/" . $_POST['search_startmonth']. "/" .$_POST['search_startday'] . "'";
                            $view_where.= " AND update_date >= '" . $_POST['search_startyear'] . "/" . $_POST['search_startmonth']. "/" .$_POST['search_startday'] . "'";
                            break;
                        case 'search_endyear':		// 登録更新日（TO）
                            $date = sfGetTimestamp($_POST['search_endyear'], $_POST['search_endmonth'], $_POST['search_endday']);
                            $date = date('Y/m/d', strtotime($date) + 86400);
                            $where.= " AND update_date < date('" . $date . "')";
                            $view_where.= " AND update_date < date('" . $date . "')";
                            break;
                        case 'search_product_flag':	//種別
                            global $arrSTATUS;
                            $search_product_flag = sfSearchCheckBoxes($val);
                            if($search_product_flag != "") {
                                $where.= " AND product_flag LIKE ?";
                                $view_where.= " AND product_flag LIKE ?";
                                $arrval[] = $search_product_flag;
                            }
                            break;
                        case 'search_status':		// ステータス
                            $tmp_where = "";
                            foreach ($val as $element){
                                if ($element != ""){
                                    if ($tmp_where == ""){
                                        $tmp_where.="AND (status LIKE ? ";
                                    }else{
                                        $tmp_where.="OR status LIKE ? ";
                                    }
                                    $arrval[]=$element;
                                }
                            }
                            if ($tmp_where != ""){
                                $tmp_where.=")";
                                $where.= " $tmp_where";
                                $view_where.= " $tmp_where";
                            }
                            break;
                        default:
                            break;
                    }
                }

                $order = "update_date DESC, product_id DESC";
                $objQuery = new SC_Query();

                switch($_POST['mode']) {
                case 'csv':
                    // オプションの指定
                    $option = "ORDER BY $order";
                    // CSV出力タイトル行の作成
                    $arrOutput = sfSwapArray(sfgetCsvOutput(1, " WHERE csv_id = 1 AND status = 1"));

                    if (count($arrOutput) <= 0) break;

                    $arrOutputCols = $arrOutput['col'];
                    $arrOutputTitle = $arrOutput['disp_name'];

                    $head = sfGetCSVList($arrOutputTitle);

                    $data = lfGetProductsCSV($where, $option, $arrval, $arrOutputCols);

                    // CSVを送信する。
                    sfCSVDownload($head.$data);
                    exit;
                    break;
                case 'delete_all':
                    // 検索結果をすべて削除
                    $where = "product_id IN (SELECT product_id FROM vw_products_nonclass AS noncls  WHERE $where)";
                    $sqlval['del_flg'] = 1;
                    $objQuery->update("dtb_products", $sqlval, $where, $arrval);
                    break;
                default:
                    // 読み込む列とテーブルの指定
                    $col = "product_id, name, category_id, main_list_image, status, product_code, price01, price02, stock, stock_unlimited";
                    $from = "vw_products_nonclass AS noncls ";

                    // 行数の取得
                    $linemax = $objQuery->count("dtb_products", $view_where, $arrval);
                    $this->tpl_linemax = $linemax;				// 何件が該当しました。表示用

                    // ページ送りの処理
                    if(is_numeric($_POST['search_page_max'])) {
                        $page_max = $_POST['search_page_max'];
                    } else {
                        $page_max = SEARCH_PMAX;
                    }

                    // ページ送りの取得
                    $objNavi = new SC_PageNavi($this->arrHidden['search_pageno'], $linemax, $page_max, "fnNaviSearchPage", NAVI_PMAX);
                    $startno = $objNavi->start_row;
                    $this->arrPagenavi = $objNavi->arrPagenavi;

                    //キャンペーン商品検索時は、全結果の商品IDを変数に格納する
                    if(isset($_POST['search_mode']) && $_POST['search_mode'] == 'campaign') {
                        $arrRet = $objQuery->select($col, $from, $where, $arrval);
                        if(count($arrRet) > 0) {
                            $arrRet = sfSwapArray($arrRet);
                            $pid = implode("-", $arrRet['product_id']);
                            $this->arrHidden['campaign_product_id'] = $pid;
                        }
                    }

                    // 取得範囲の指定(開始行番号、行数のセット)
                    if(DB_TYPE != "mysql") $objQuery->setlimitoffset($page_max, $startno);
                    // 表示順序
                    $objQuery->setorder($order);
//
//                    // viewも絞込みをかける(mysql用)
//                    sfViewWhere("&&noncls_where&&", $view_where, $arrval, $objQuery->order . " " .  $objQuery->setlimitoffset($page_max, $startno, true));
//
                    // 検索結果の取得
                    $this->arrProducts = $objQuery->select($col, $from, $where, $arrval);

                    break;
                }
            }
        }

        // カテゴリの読込
        $this->arrCatList = SC_Utils_Ex::sfGetCategoryList();
        $this->arrCatIDName = $this->lfGetIDName($this->arrCatList);

        // 画面の表示
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

    // 取得文字列の変換
    function lfConvertParam() {
        global $objPage;
        /*
         *	文字列の変換
         *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *	C :  「全角ひら仮名」を「全角かた仮名」に変換
         *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
         */
        $arrConvList['search_name'] = "KVa";
        $arrConvList['search_product_code'] = "KVa";

        // 文字変換
        foreach ($arrConvList as $key => $val) {
            // POSTされてきた値のみ変換する。
            if(isset($objPage->arrForm[$key])) {
                $objPage->arrForm[$key] = mb_convert_kana($objPage->arrForm[$key] ,$val);
            }
        }
    }

    // エラーチェック
    // 入力エラーチェック
    function lfCheckError() {
        $objErr = new SC_CheckError();
        $objErr->doFunc(array("商品ID", "search_product_id"), array("NUM_CHECK"));
        $objErr->doFunc(array("開始日", "search_startyear", "search_startmonth", "search_startday"), array("CHECK_DATE"));
        $objErr->doFunc(array("終了日", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_DATE"));
        $objErr->doFunc(array("開始日", "終了日", "search_startyear", "search_startmonth", "search_startday", "search_endyear", "search_endmonth", "search_endday"), array("CHECK_SET_TERM"));
        return $objErr->arrErr;
    }

    // チェックボックス用WHERE文作成
    function lfGetCBWhere($key, $max) {
        $str = "";
        $find = false;
        for ($cnt = 1; $cnt <= $max; $cnt++) {
            if ($_POST[$key . $cnt] == "1") {
                $str.= "1";
                $find = true;
            } else {
                $str.= "_";
            }
        }
        if (!$find) {
            $str = "";
        }
        return $str;
    }

    // カテゴリIDをキー、カテゴリ名を値にする配列を返す。
    function lfGetIDName($arrCatList) {
        $max = count($arrCatList);
        for ($cnt = 0; $cnt < $max; $cnt++ ) {
            $key = isset($arrCatList[$cnt]['category_id']) ? $arrCatList[$cnt]['category_id'] : "";
            $val = isset($arrCatList[$cnt]['category_name']) ? $arrCatList[$cnt]['category_name'] : "";
            $arrRet[$key] = $val;
        }
        return $arrRet;
    }
}
?>
