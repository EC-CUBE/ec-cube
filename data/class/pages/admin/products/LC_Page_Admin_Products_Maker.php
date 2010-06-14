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
 * メーカー管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Products_Maker.php 16741 2007-11-08 00:43:24Z adachi $
 */
class LC_Page_Admin_Products_Maker extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/maker.tpl';
        $this->tpl_subnavi = 'products/subnavi.tpl';
        $this->tpl_subno = 'maker';
        $this->tpl_subtitle = 'メーカー管理';
        $this->tpl_mainno = 'products';
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
                if($_POST['maker_id'] == "") {
                    $this->lfInsertClass($this->arrForm);	// 新規作成
                } else {
                    $this->lfUpdateClass($this->arrForm);	// 既存編集
                }
                // 再表示
                $this->reload();
            } else {
                // POSTデータを引き継ぐ
                $this->tpl_maker_id = $_POST['maker_id'];
            }
            break;
        // 削除
        case 'delete':
            $objDb->sfDeleteRankRecord("dtb_maker", "maker_id", $_POST['maker_id'], "", true);
            // 再表示
            $this->reload();
            break;
        // 編集前処理
        case 'pre_edit':
            // 編集項目をDBより取得する。
            $where = "maker_id = ?";
            $arrRet = $objQuery->select("name", "dtb_maker", $where, array($_POST['maker_id']));
            // 入力項目にカテゴリ名を入力する。
            $this->arrForm['name'] = $arrRet[0]['name'];
            // POSTデータを引き継ぐ
            $this->tpl_maker_id = $_POST['maker_id'];
        break;
        case 'down':
            $objDb->sfRankDown("dtb_maker", "maker_id", $_POST['maker_id']);
            // 再表示
            $this->reload();
            break;
        case 'up':
            $objDb->sfRankUp("dtb_maker", "maker_id", $_POST['maker_id']);
            // 再表示
            $this->reload();
            break;
        default:
            break;
        }

        // 規格の読込
        $where = "del_flg <> 1";
        $objQuery->setOrder("rank DESC");
        $this->arrMaker = $objQuery->select("maker_id, name", "dtb_maker", $where);

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
        $sqlval['name'] = $arrData['name'];
        $sqlval['rank'] = $objQuery->max("dtb_maker", "rank") + 1;
        $sqlval['creator_id'] = $_SESSION['member_id'];
        $sqlval['update_date'] = "Now()";
        $sqlval['create_date'] = "Now()";
        // INSERTの実行
        $ret = $objQuery->insert("dtb_maker", $sqlval);
        return $ret;
    }

    /* DBへの更新 */
    function lfUpdateClass($arrData) {
        $objQuery = new SC_Query();
        // UPDATEする値を作成する。
        $sqlval['name'] = $arrData['name'];
        $sqlval['update_date'] = "Now()";
        $where = "maker_id = ?";
        // UPDATEの実行
        $ret = $objQuery->update("dtb_maker", $sqlval, $where, array($_POST['maker_id']));
        return $ret;
    }

    /* 取得文字列の変換 */
    function lfConvertParam($array) {
        // 文字変換
        $arrConvList['name'] = "KVa";

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
        $objErr->doFunc(array("メーカー名", "name", SMTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        if(!isset($objErr->arrErr['name'])) {
            $objQuery = new SC_Query();
            $arrRet = $objQuery->select("maker_id, name", "dtb_maker", "del_flg = 0 AND name = ?", array($_POST['name']));
            // 編集中のレコード以外に同じ名称が存在する場合
            if ($arrRet[0]['maker_id'] != $_POST['maker_id'] && $arrRet[0]['name'] == $_POST['name']) {
                $objErr->arrErr['name'] = "※ 既に同じ内容の登録が存在します。<br>";
            }
        }
        return $objErr->arrErr;
    }
}
?>
