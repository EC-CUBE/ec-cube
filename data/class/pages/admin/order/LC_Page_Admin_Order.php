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

/* ペイジェント決済モジュール連携用 */
if (file_exists(MODULE_REALDIR . 'mdl_paygent/include.php') === TRUE) {
    require_once(MODULE_REALDIR . 'mdl_paygent/include.php');
}

/**
 * 受注管理 のページクラス
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Order extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'order/index.tpl';
        $this->tpl_subnavi = 'order/subnavi.tpl';
        $this->tpl_mainno = 'order';
        $this->tpl_subno = 'index';
        $this->tpl_pager = TEMPLATE_REALDIR . 'admin/pager.tpl';
        $this->tpl_subtitle = '受注管理';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrORDERSTATUS = $masterData->getMasterData("mtb_order_status");
        $this->arrORDERSTATUS_COLOR = $masterData->getMasterData("mtb_order_status_color");
        $this->arrSex = $masterData->getMasterData("mtb_sex");
        $this->arrPageMax = $masterData->getMasterData("mtb_page_max");

        /* ペイジェント決済モジュール連携用 */
        if(function_exists("sfPaygentOrderPage")) {
            $this->arrDispKind = sfPaygentOrderPage();
        }
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
        $objDb = new SC_Helper_DB_Ex();
        $objSess = new SC_Session();
        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParam();
        $this->objFormParam->setParam($_POST);

        $this->objFormParam->splitParamCheckBoxes('search_order_sex');
        $this->objFormParam->splitParamCheckBoxes('search_payment_id');

        // 検索ワードの引き継ぎ
        foreach ($_POST as $key => $val) {
            if (ereg("^search_", $key)) {
                switch($key) {
                    case 'search_order_sex':
                    case 'search_payment_id':
                        $this->arrHidden[$key] = SC_Utils_Ex::sfMergeParamCheckBoxes($val);
                        break;
                    default:
                        $this->arrHidden[$key] = $val;
                        break;
                }
            }
        }

        // ページ送り用
        $this->arrHidden['search_pageno'] =
        isset($_POST['search_pageno']) ? $_POST['search_pageno'] : "";

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        if (!isset($arrRet)) $arrRet = array();

        if($this->getMode() == 'delete') {
            if(SC_Utils_Ex::sfIsInt($_POST['order_id'])) {
                $objQuery = new SC_Query();
                $where = "order_id = ?";
                $sqlval['del_flg'] = '1';
                $objQuery->update("dtb_order", $sqlval, $where, array($_POST['order_id']));
            }
        }

        switch($this->getMode()) {
            case 'delete':
            case 'csv':
            case 'delete_all':
            case 'search':
                // 入力値の変換
                $this->objFormParam->convParam();
                $this->arrErr = $this->lfCheckError($arrRet);
                $arrRet = $this->objFormParam->getHashArray();
                // 入力なし
                if (count($this->arrErr) == 0) {
                    $where = "del_flg = 0";
                    foreach ($arrRet as $key => $val) {
                        if($val == "") {
                            continue;
                        }

                        $dbFactory = SC_DB_DBFactory::getInstance();
                        switch ($key) {

                            case 'search_product_name':
                                    $where .= " AND (SELECT COUNT(*) FROM dtb_order_detail od WHERE od.order_id = dtb_order.order_id AND od.product_name ILIKE ?) > 0";
                                    $nonsp_val = mb_ereg_replace("[ 　]+","",$val);
                                    $arrval[] = "%$nonsp_val%";
                                break;
                            case 'search_order_name':
                                $where .= " AND " . $dbFactory->concatColumn(array("order_name01", "order_name02")) . " ILIKE ?";
                                $nonsp_val = mb_ereg_replace("[ 　]+","",$val);
                                $arrval[] = "%$nonsp_val%";
                                break;
                            case 'search_order_kana':
                                $where .= " AND " . $dbFactory->concatColumn(array("order_kana01", "order_kana02")) . " ILIKE ?";
                                $nonsp_val = mb_ereg_replace("[ 　]+","",$val);
                                $arrval[] = "%$nonsp_val%";
                                break;
                            case 'search_order_id1':
                                $where .= " AND order_id >= ?";
                                $arrval[] = $val;
                                break;
                            case 'search_order_id2':
                                $where .= " AND order_id <= ?";
                                $arrval[] = $val;
                                break;
                            case 'search_order_sex':
                                $tmp_where = "";
                                foreach($val as $element) {
                                    if($element != "") {
                                        if($tmp_where == "") {
                                            $tmp_where .= " AND (order_sex = ?";
                                        } else {
                                            $tmp_where .= " OR order_sex = ?";
                                        }
                                        $arrval[] = $element;
                                    }
                                }

                                if($tmp_where != "") {
                                    $tmp_where .= ")";
                                    $where .= " $tmp_where ";
                                }
                                break;
                            case 'search_order_tel':
                                $where .= " AND (" . $dbFactory->concatColumn(array("order_tel01", "order_tel02", "order_tel03")) . " ILIKE ?)";
                                $nonmark_val = ereg_replace("[()-]+","",$val);
                                $arrval[] = "%$nonmark_val%";
                                break;
                            case 'search_order_email':
                                $where .= " AND order_email ILIKE ?";
                                $arrval[] = "%$val%";
                                break;
                            case 'search_payment_id':
                                $tmp_where = "";
                                foreach($val as $element) {
                                    if($element != "") {
                                        if($tmp_where == "") {
                                            $tmp_where .= " AND (payment_id = ?";
                                        } else {
                                            $tmp_where .= " OR payment_id = ?";
                                        }
                                        $arrval[] = $element;
                                    }
                                }

                                if($tmp_where != "") {
                                    $tmp_where .= ")";
                                    $where .= " $tmp_where ";
                                }
                                break;
                            case 'search_total1':
                                $where .= " AND total >= ?";
                                $arrval[] = $val;
                                break;
                            case 'search_total2':
                                $where .= " AND total <= ?";
                                $arrval[] = $val;
                                break;
                            case 'search_sorderyear':
                                $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_sorderyear'], $_POST['search_sordermonth'], $_POST['search_sorderday']);
                                $where.= " AND create_date >= ?";
                                $arrval[] = $date;
                                break;
                            case 'search_eorderyear':
                                $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_eorderyear'], $_POST['search_eordermonth'], $_POST['search_eorderday'], true);
                                $where.= " AND create_date <= ?";
                                $arrval[] = $date;
                                break;
                            case 'search_supdateyear':
                                $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_supdateyear'], $_POST['search_supdatemonth'], $_POST['search_supdateday']);
                                $where.= " AND update_date >= ?";
                                $arrval[] = $date;
                                break;
                            case 'search_eupdateyear':
                                $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_eupdateyear'], $_POST['search_eupdatemonth'], $_POST['search_eupdateday'], true);
                                $where.= " AND update_date <= ?";
                                $arrval[] = $date;
                                break;
                            case 'search_sbirthyear':
                                $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_sbirthyear'], $_POST['search_sbirthmonth'], $_POST['search_sbirthday']);
                                $where.= " AND order_birth >= ?";
                                $arrval[] = $date;
                                break;
                            case 'search_ebirthyear':
                                $date = SC_Utils_Ex::sfGetTimestamp($_POST['search_ebirthyear'], $_POST['search_ebirthmonth'], $_POST['search_ebirthday'], true);
                                $where.= " AND order_birth <= ?";
                                $arrval[] = $date;
                                break;
                            case 'search_order_status':
                                $where.= " AND status = ?";
                                $arrval[] = $val;
                                break;
                            default:
                                if (!isset($arrval)) $arrval = array();
                                break;
                        }
                    }

                    $order = "update_date DESC";
                    switch($this->getMode()) {
                        case 'csv':

                            require_once(CLASS_EX_REALDIR . "helper_extends/SC_Helper_CSV_Ex.php");
                            $objCSV = new SC_Helper_CSV_Ex();
                            // オプションの指定
                            $option = "ORDER BY $order";

                            // CSV出力タイトル行の作成
                            $arrCsvOutput = SC_Utils_Ex::sfSwapArray($objCSV->sfGetCsvOutput(3, 'status = 1'));

                            if (count($arrCsvOutput) <= 0) break;

                            $arrCsvOutputCols = $arrCsvOutput['col'];
                            $arrCsvOutputConvs = $arrCsvOutput['conv'];
                            $arrCsvOutputTitle = $arrCsvOutput['disp_name'];
                            $head = SC_Utils_Ex::sfGetCSVList($arrCsvOutputTitle);
                            $data = $objCSV->lfGetCSV("dtb_order", $where, $option, $arrval, $arrCsvOutputCols, $arrCsvOutputConvs);

                            // CSVを送信する。
                            list($fime_name, $data) = SC_Utils_Ex::sfGetCSVData($head.$data);
                            $this->sendResponseCSV($fime_name, $data);
                            exit;
                            break;
                        case 'delete_all':
                            // 検索結果をすべて削除
                            $sqlval['del_flg'] = 1;
                            $objQuery = new SC_Query();
                            $objQuery->update("dtb_order", $sqlval, $where, $arrval);
                            break;
                        default:
                            // 読み込む列とテーブルの指定
                            $col = "*";
                            $from = "dtb_order";

                            $objQuery = new SC_Query();
                            // 行数の取得
                            $linemax = $objQuery->count($from, $where, $arrval);
                            $this->tpl_linemax = $linemax;               // 何件が該当しました。表示用
                            // ページ送りの処理
                            if(is_numeric($_POST['search_page_max'])) {
                                $page_max = $_POST['search_page_max'];
                            } else {
                                $page_max = SEARCH_PMAX;
                            }

                            // ページ送りの取得
                            $objNavi = new SC_PageNavi($this->arrHidden['search_pageno'],
                            $linemax, $page_max,
                                               "fnNaviSearchPage", NAVI_PMAX);
                            $startno = $objNavi->start_row;
                            $this->arrPagenavi = $objNavi->arrPagenavi;

                            // 取得範囲の指定(開始行番号、行数のセット)
                            $objQuery->setLimitOffset($page_max, $startno);
                            // 表示順序
                            $objQuery->setOrder($order);
                            // 検索結果の取得
                            $this->arrResults = $objQuery->select($col, $from, $where, $arrval);
                    }
                }
                break;

                        default:
                            break;
        }

        $objDate = new SC_Date();
        // 登録・更新日検索用
        $objDate->setStartYear(RELEASE_YEAR);
        $objDate->setEndYear(DATE("Y"));
        $this->arrRegistYear = $objDate->getYear();
        // 生年月日検索用
        $objDate->setStartYear(BIRTH_YEAR);
        $objDate->setEndYear(DATE("Y"));
        $this->arrBirthYear = $objDate->getYear();
        // 月日の設定
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();

        // 入力値の取得
        $this->arrForm = $this->objFormParam->getFormParamList();
        // 支払い方法の取得
        $arrRet = $objDb->sfGetPayment();
        $this->arrPayment = SC_Utils_Ex::sfArrKeyValue($arrRet, 'payment_id', 'payment_method');
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /* パラメータ情報の初期化 */
    function lfInitParam() {
        $this->objFormParam->addParam("注文番号1", "search_order_id1", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("注文番号2", "search_order_id2", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("対応状況", "search_order_status", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("顧客名", "search_order_name", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("顧客名(カナ)", "search_order_kana", STEXT_LEN, "KVCa", array("KANA_CHECK","MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("性別", "search_order_sex", INT_LEN, "n", array("MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("年齢1", "search_age1", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("年齢2", "search_age2", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("メールアドレス", "search_order_email", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("TEL", "search_order_tel", STEXT_LEN, "KVa", array("MAX_LENGTH_CHECK"));
        $this->objFormParam->addParam("支払い方法", "search_payment_id", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("購入金額1", "search_total1", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("購入金額2", "search_total2", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("表示件数", "search_page_max", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        // 受注日
        $this->objFormParam->addParam("開始年", "search_sorderyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("開始月", "search_sordermonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("開始日", "search_sorderday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("終了年", "search_eorderyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("終了月", "search_eordermonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("終了日", "search_eorderday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        // 更新日
        $this->objFormParam->addParam("開始年", "search_supdateyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("開始月", "search_supdatemonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("開始日", "search_supdateday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("終了年", "search_eupdateyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("終了月", "search_eupdatemonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("終了日", "search_eupdateday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        // 生年月日
        $this->objFormParam->addParam("開始年", "search_sbirthyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("開始月", "search_sbirthmonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("開始日", "search_sbirthday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("終了年", "search_ebirthyear", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("終了月", "search_ebirthmonth", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("終了日", "search_ebirthday", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $this->objFormParam->addParam("購入商品","search_product_name",STEXT_LEN,"KVa",array("MAX_LENGTH_CHECK"));

    }

    /* 入力内容のチェック */
    function lfCheckError() {
        // 入力データを渡す。
        $arrRet =  $this->objFormParam->getHashArray();
        $objErr = new SC_CheckError($arrRet);
        $objErr->arrErr = $this->objFormParam->checkError();

        // 特殊項目チェック
        $objErr->doFunc(array("注文番号1", "注文番号2", "search_order_id1", "search_order_id2"), array("GREATER_CHECK"));
        $objErr->doFunc(array("年齢1", "年齢2", "search_age1", "search_age2"), array("GREATER_CHECK"));
        $objErr->doFunc(array("購入金額1", "購入金額2", "search_total1", "search_total2"), array("GREATER_CHECK"));
        // 受注日
        $objErr->doFunc(array("開始", "search_sorderyear", "search_sordermonth", "search_sorderday"), array("CHECK_DATE"));
        $objErr->doFunc(array("終了", "search_eorderyear", "search_eordermonth", "search_eorderday"), array("CHECK_DATE"));
        $objErr->doFunc(array("開始", "終了", "search_sorderyear", "search_sordermonth", "search_sorderday", "search_eorderyear", "search_eordermonth", "search_eorderday"), array("CHECK_SET_TERM"));
        // 更新日
        $objErr->doFunc(array("開始", "search_supdateyear", "search_supdatemonth", "search_supdateday"), array("CHECK_DATE"));
        $objErr->doFunc(array("終了", "search_eupdateyear", "search_eupdatemonth", "search_eupdateday"), array("CHECK_DATE"));
        $objErr->doFunc(array("開始", "終了", "search_supdateyear", "search_supdatemonth", "search_supdateday", "search_eupdateyear", "search_eupdatemonth", "search_eupdateday"), array("CHECK_SET_TERM"));
        // 生年月日
        $objErr->doFunc(array("開始", "search_sbirthyear", "search_sbirthmonth", "search_sbirthday"), array("CHECK_DATE"));
        $objErr->doFunc(array("終了", "search_ebirthyear", "search_ebirthmonth", "search_ebirthday"), array("CHECK_DATE"));
        $objErr->doFunc(array("開始", "終了", "search_sbirthyear", "search_sbirthmonth", "search_sbirthday", "search_ebirthyear", "search_ebirthmonth", "search_ebirthday"), array("CHECK_SET_TERM"));

        return $objErr->arrErr;
    }


}
?>
