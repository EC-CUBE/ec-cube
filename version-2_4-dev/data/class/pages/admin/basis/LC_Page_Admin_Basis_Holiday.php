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
 * 会員規約設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Basis_Holiday.php 16741 2007-11-08 00:43:24Z adachi $
 */
class LC_Page_Admin_Basis_Holiday extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/holiday.tpl';
        $this->tpl_subnavi = 'basis/subnavi.tpl';
        $this->tpl_subno = 'holiday';
        $this->tpl_subtitle = '定休日登録';
        $this->tpl_mainno = 'basis';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $conn = new SC_DBConn();
        $objView = new SC_AdminView();
        $objSess = new SC_Session();
        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();

        $objDate = new SC_Date();
        $this->arrMonth = $objDate->getMonth();
        $this->arrDay = $objDate->getDay();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        // 要求判定
        switch($_POST['mode']) {
        // 編集処理
        case 'edit':
            // POST値の引き継ぎ
            $this->arrForm = $_POST;
            // 入力文字の変換
            $this->arrForm = $this->lfConvertParam($this->arrForm);

            // エラーチェック
            $this->arrErr = $this->lfErrorCheck();
            if(count($this->arrErr) <= 0) {
                if($_POST['holiday_id'] == "") {
                    $this->lfInsertClass($this->arrForm);	// 新規作成
                } else {
                    $this->lfUpdateClass($this->arrForm);	// 既存編集
                }
                // 再表示
                $this->reload();
            } else {
                // POSTデータを引き継ぐ
                $this->tpl_holiday_id = $_POST['holiday_id'];
            }
            break;
        // 削除
        case 'delete':
            $objDb->sfDeleteRankRecord("dtb_holiday", "holiday_id", $_POST['holiday_id'], "", true);
            // 再表示
            $this->reload();
            break;
        // 編集前処理
        case 'pre_edit':
            // 編集項目をDBより取得する。
            $where = "holiday_id = ?";
            $arrRet = $objQuery->select("title, month, day", "dtb_holiday", $where, array($_POST['holiday_id']));
            // 入力項目にカテゴリ名を入力する。
            $this->arrForm['title'] = $arrRet[0]['title'];
            $this->arrForm['month'] = $arrRet[0]['month'];
            $this->arrForm['day'] = $arrRet[0]['day'];
            // POSTデータを引き継ぐ
            $this->tpl_holiday_id = $_POST['holiday_id'];
        break;
        case 'down':
            $objDb->sfRankDown("dtb_holiday", "holiday_id", $_POST['holiday_id']);
            // 再表示
            $this->reload();
            break;
        case 'up':
            $objDb->sfRankUp("dtb_holiday", "holiday_id", $_POST['holiday_id']);
            // 再表示
            $this->reload();
            break;
        default:
            break;
        }

        // 規格の読込
        $where = "del_flg <> 1";
        $objQuery->setorder("rank DESC");
        $this->arrHoliday = $objQuery->select("holiday_id, title, month, day", "dtb_holiday", $where);

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

    /* DBへの挿入 */
    function lfInsertClass($arrData) {
        $objQuery = new SC_Query();
        // INSERTする値を作成する。
        $sqlval['title'] = $arrData['title'];
        $sqlval['month'] = $arrData['month'];
        $sqlval['day'] = $arrData['day'];
        $sqlval['creator_id'] = $_SESSION['member_id'];
        $sqlval['rank'] = $objQuery->max("dtb_holiday", "rank") + 1;
        $sqlval['update_date'] = "Now()";
        $sqlval['create_date'] = "Now()";
        // INSERTの実行
        $ret = $objQuery->insert("dtb_holiday", $sqlval);
        return $ret;
    }

    /* DBへの更新 */
    function lfUpdateClass($arrData) {
        $objQuery = new SC_Query();
        // UPDATEする値を作成する。
        $sqlval['title'] = $arrData['title'];
        $sqlval['month'] = $arrData['month'];
        $sqlval['day'] = $arrData['day'];
        $sqlval['update_date'] = "Now()";
        $where = "holiday_id = ?";
        // UPDATEの実行
        $ret = $objQuery->update("dtb_holiday", $sqlval, $where, array($_POST['holiday_id']));
        return $ret;
    }

    /* 取得文字列の変換 */
    function lfConvertParam($array) {
        // 文字変換
        $arrConvList['title'] = "KVa";
        $arrConvList['month'] = "n";
        $arrConvList['day'] = "n";

        foreach ($arrConvList as $key => $val) {
            // POSTされてきた値のみ変換する。
            if(isset($array[$key])) {
                $array[$key] = mb_convert_kana($array[$key] ,$val);
            }
        }
        return $array;
    }

    /* 入力エラーチェック */
    function lfErrorCheck() {
        $objErr = new SC_CheckError();
        $objErr->doFunc(array("タイトル", "title", SMTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("月", "month", INT_LEN), array("SELECT_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("日", "day", INT_LEN), array("SELECT_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        if(!isset($objErr->arrErr['date'])) {
            $objQuery = new SC_Query();
            $where = "del_flg = 0 AND month = ? AND day = ?";
            $arrval = array($_POST['month'], $_POST['day']);
            if (!empty($_POST['holiday_id'])) {
                $where .= " AND holiday_id <> ?";
                $arrval[] = $_POST['holiday_id'];
            }
            $arrRet = $objQuery->select("count(holiday_id)", "dtb_holiday", $where, $arrval);
            // 編集中のレコード以外に同じ日付が存在する場合
            if ($arrRet[0]['count'] > 0) {
                $objErr->arrErr['date'] = "※ 既に同じ日付の登録が存在します。<br>";
            }
        }
        return $objErr->arrErr;
    }
}
?>
