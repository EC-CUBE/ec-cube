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
 * 商品管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Products extends LC_Page_Admin {

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
        $this->tpl_pager = TEMPLATE_REALDIR . 'admin/pager.tpl';
        $this->tpl_subtitle = '商品マスタ';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPageMax = $masterData->getMasterData("mtb_page_max");
        $this->arrDISP = $masterData->getMasterData("mtb_disp");
        $this->arrSTATUS = $masterData->getMasterData("mtb_status");
        $this->arrPRODUCTSTATUS_COLOR = $masterData->getMasterData("mtb_product_status_color");
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
        $objDb = new SC_Helper_DB_Ex();
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

        // POST値の引き継ぎ
        $this->arrForm = $_POST;

        // 検索ワードの引き継ぎ
        foreach ($_POST as $key => $val) {
            if (ereg("^search_", $key) || ereg("^campaign_", $key)) {
                switch($key) {
                    case 'search_product_flag':
                    case 'search_status':
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
        }

        // ページ送り用
        $this->arrHidden['search_pageno'] = isset($_POST['search_pageno']) ? $_POST['search_pageno'] : "";

        // 商品削除
        if ($_POST['mode'] == "delete") {
            $objQuery = new SC_Query();
            $objQuery->delete("dtb_products",
                          "product_id = ?", array($_POST['product_id']));

            // 子テーブル(商品規格)の削除
            $objQuery->delete("dtb_products_class", "product_id = ?", array($_POST['product_id']));

            // お気に入り商品削除
            $objQuery->delete("dtb_customer_favorite_products", "product_id = ?", array($_POST['product_id']));

            // 件数カウントバッチ実行
            $objDb->sfCategory_Count($objQuery);
            $objDb->sfMaker_Count($objQuery);
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

                    if($val == "") {
                        continue;
                    }

                    switch ($key) {
                        case 'search_product_id': // 商品ID
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
                        case 'search_name': // 商品名
                            $where .= " AND name ILIKE ?";
                            $view_where .= " AND name ILIKE ?";
                            $arrval[] = "%$val%";
                            break;
                        case 'search_category_id': // カテゴリー
                            list($tmp_where, $tmp_arrval) = $objDb->sfGetCatWhere($val);
                            if($tmp_where != "") {
                                $where.= " AND product_id IN (SELECT product_id FROM dtb_product_categories WHERE " . $tmp_where . ")";
                                $view_where.= " AND product_id IN (SELECT product_id FROM dtb_product_categories WHERE " . $tmp_where . ")";
                                $arrval = array_merge((array)$arrval, (array)$tmp_arrval);
                            }
                            break;
                        case 'search_product_code': // 商品コード
                            $where .= " AND product_id IN (SELECT product_id FROM dtb_products_class WHERE product_code ILIKE ? GROUP BY product_id)";
                            $view_where .= " AND EXISTS (SELECT product_id FROM dtb_products_class as cls WHERE cls.product_code ILIKE ? AND dtb_products.product_id = cls.product_id GROUP BY cls.product_id )";
                            $arrval[] = "%$val%";
                            break;
                        case 'search_startyear': // 登録更新日（FROM）
                            $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_startyear'], $_POST['search_startmonth'], $_POST['search_startday']);
                            $date = date('Y/m/d', strtotime($date));
                            $where.= " AND update_date >= date(?)";
                            $view_where.= " AND update_date >= date(?)";
                            $arrval[] = $date;
                            break;
                        case 'search_endyear': // 登録更新日（TO）
                            $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_endyear'], $_POST['search_endmonth'], $_POST['search_endday']);
                            $date = date('Y/m/d', strtotime($date) + 86400);
                            $where.= " AND update_date < date(?)";
                            $view_where.= " AND update_date < date(?)";
                            $arrval[] = $date;
                            break;
                        case 'search_product_flag': //種別
                            if(count($val) > 0) {
                                $where .= " AND product_id IN (SELECT product_id FROM dtb_product_status WHERE product_status_id IN (";
                                $view_where .= " AND product_id IN (SELECT product_id FROM dtb_product_status WHERE product_status_id IN (";
                                foreach($val as $param) {
                                    $where .= "?,";
                                    $view_where .= "?,";
                                    $arrval[] = $param;
                                }
                                $where = preg_replace("/,$/", "))", $where);
                                $view_where = preg_replace("/,$/", "))", $where);
                            }
                            break;
                        case 'search_status': // ステータス
                            $tmp_where = "";
                            foreach ($val as $element){
                                if ($element != ""){
                                    if ($tmp_where == ""){
                                        $tmp_where.="AND (status = ? ";
                                    }else{
                                        $tmp_where.="OR status = ? ";
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
                $objProduct = new SC_Product();
                switch($_POST['mode']) {
                    case 'csv':
                        require_once(CLASS_EX_REALDIR . "helper_extends/SC_Helper_CSV_Ex.php");

                        $objCSV = new SC_Helper_CSV_Ex();

                        // CSVを送信する。正常終了の場合、終了。
                        $objCSV->sfDownloadProductsCsv($where, $arrval, $order, true);
                        // FIXME: sendResponseに渡した方が良いのか？
//                        $data = $objCSV->sfDownloadProductsCsv($where, $arrval, $order);
//                        $this->sendResponseCSV($fime_name, $data);
                        exit;
                        break;
                    case 'delete_all':
                        // 検索結果をすべて削除
                        $where = "product_id IN (SELECT product_id FROM "
                            . $objProduct->alldtlSQL() . " WHERE $where)";
                        $sqlval['del_flg'] = 1;
                        $objQuery->update("dtb_products", $sqlval, $where, $arrval);
                        $objQuery->delete("dtb_customer_favorite_products", $where, $arrval);

                        // 件数カウントバッチ実行
                        $objDb->sfCategory_Count($objQuery);

                        break;
                    default:
                        // 読み込む列とテーブルの指定
                        $col = "product_id, name, main_list_image, status, product_code_min, product_code_max, price02_min, price02_max, stock_min, stock_max, stock_unlimited_min, stock_unlimited_max, update_date";
                        $from = $objProduct->alldtlSQL();

                        // 行数の取得
                        $linemax = $objQuery->count("dtb_products", $view_where, $arrval);
                        $this->tpl_linemax = $linemax; // 何件が該当しました。表示用

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
                        $objQuery->setLimitOffset($page_max, $startno);
                        // 表示順序
                        $objQuery->setOrder($order);

                        // 検索結果の取得
                        $this->arrProducts = $objQuery->select($col, $from, $where, $arrval);
                        
                        // 各商品ごとのカテゴリIDを取得
                        if (count($this->arrProducts) > 0) {
                            foreach ($this->arrProducts as $key => $val) {
                                $this->arrProducts[$key]["categories"] = $objDb->sfGetCategoryId($val["product_id"], 0, true);
                                $objDb->g_category_on = false;
                            }
                        }
                }
            }
        }

        // カテゴリの読込
        list($this->arrCatKey, $this->arrCatVal) = $objDb->sfGetLevelCatList(false);
        $this->arrCatList = $this->lfGetIDName($this->arrCatKey, $this->arrCatVal);
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
         *  文字列の変換
         *  K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
         *  C :  「全角ひら仮名」を「全角かた仮名」に変換
         *  V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します
         *  n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
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
    function lfGetIDName($arrCatKey, $arrCatVal) {
        $max = count($arrCatKey);
        for ($cnt = 0; $cnt < $max; $cnt++ ) {
            $key = isset($arrCatKey[$cnt]) ? $arrCatKey[$cnt] : "";
            $val = isset($arrCatVal[$cnt]) ? $arrCatVal[$cnt] : "";
            $arrRet[$key] = $val;
        }
        return $arrRet;
    }
}
?>
