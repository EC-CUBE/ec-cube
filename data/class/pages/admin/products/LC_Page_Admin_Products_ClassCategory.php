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
 * 規格分類 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:LC_Page_Admin_Products_ClassCategory.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class LC_Page_Admin_Products_ClassCategory extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/classcategory.tpl';
        $this->tpl_subnavi = 'products/subnavi.tpl';
        $this->tpl_subno = 'class';
        $this->tpl_subtitle = '規格登録';
        $this->tpl_mainno = 'products';
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
        $objQuery = new SC_Query();
        $objDb = new SC_Helper_DB_Ex();

        // 認証可否の判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        $get_check = false;

        // 規格IDのチェック
        if(SC_Utils_Ex::sfIsInt($_GET['class_id'])) {
            // 規格名の取得
            $this->tpl_class_name = $objQuery->get("name", "dtb_class", "class_id = ?", array($_GET['class_id']));
            if($this->tpl_class_name != "") {
                // 規格IDの引き継ぎ
                $this->arrHidden['class_id'] = $_GET['class_id'];
                $get_check = true;
            }
        }

        if(!$get_check) {
            // 規格登録ページに飛ばす。
            SC_Response_Ex::sendRedirect(ADMIN_CLASS_REGIST_URL_PATH);
            exit;
        }

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        if (isset($_POST['class_id'])) {
            if (!SC_Utils_Ex::sfIsInt($_POST['class_id'])) {
                SC_Utils_Ex::sfDispError("");
            }
        }

        // 新規作成 or 編集
        switch($_POST['mode']) {
            // 登録ボタン押下
        case 'edit':
            // POST値の引き継ぎ
            $this->arrForm = $_POST;
            // 入力文字の変換
            $_POST = $this->lfConvertParam($_POST);
            // エラーチェック
            $this->arrErr = $this->lfErrorCheck();
            if(count($this->arrErr) <= 0) {
                if($_POST['classcategory_id'] == "") {
                    $this->lfInsertClass();	// DBへの書き込み
                } else {
                    $this->lfUpdateClass();	// DBへの書き込み
                }
                // 再表示
                $this->objDisplay->reload($_GET['class_id']);
                //sfReload("class_id=" . $_GET['class_id']);
            } else {
                // POSTデータを引き継ぐ
                $this->tpl_classcategory_id = $_POST['classcategory_id'];
            }
            break;
            // 削除
        case 'delete':
            // ランク付きレコードの削除
            $where = "class_id = " . SC_Utils_Ex::sfQuoteSmart($_POST['class_id']);
            $objDb->sfDeleteRankRecord("dtb_classcategory", "classcategory_id", $_POST['classcategory_id'], $where, true);
            break;
            // 編集前処理
        case 'pre_edit':
            // 編集項目をDBより取得する。
            $where = "classcategory_id = ?";
            $name = $objQuery->get("name", "dtb_classcategory", $where, array($_POST['classcategory_id']));
            // 入力項目にカテゴリ名を入力する。
            $this->arrForm['name'] = $name;
            // POSTデータを引き継ぐ
            $this->tpl_classcategory_id = $_POST['classcategory_id'];
            break;
        case 'down':
            $where = "class_id = " . SC_Utils_Ex::sfQuoteSmart($_POST['class_id']);
            $objDb->sfRankDown("dtb_classcategory", "classcategory_id", $_POST['classcategory_id'], $where);
            break;
        case 'up':
            $where = "class_id = " . SC_Utils_Ex::sfQuoteSmart($_POST['class_id']);
            $objDb->sfRankUp("dtb_classcategory", "classcategory_id", $_POST['classcategory_id'], $where);
            break;
        default:
            break;
        }

        // 規格分類の読込
        $where = "del_flg <> 1 AND class_id = ?";
        $objQuery->setOrder("rank DESC");
        $this->arrClassCat = $objQuery->select("name, classcategory_id", "dtb_classcategory", $where, array($_GET['class_id']));
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
    function lfInsertClass() {
        $objQuery = new SC_Query();
        $objQuery->begin();
        // 親規格IDの存在チェック
        $where = "del_flg <> 1 AND class_id = ?";
        $ret = 	$objQuery->get("class_id", "dtb_class", $where, array($_POST['class_id']));
        if($ret != "") {
            // INSERTする値を作成する。
            $sqlval['name'] = $_POST['name'];
            $sqlval['class_id'] = $_POST['class_id'];
            $sqlval['creator_id'] = $_SESSION['member_id'];
            $sqlval['rank'] = $objQuery->max("rank", "dtb_classcategory", $where, array($_POST['class_id'])) + 1;
            $sqlval['create_date'] = "now()";
            $sqlval['update_date'] = "now()";
            // INSERTの実行
            $sqlval['classcategory_id'] = $objQuery->nextVal('dtb_classcategory_classcategory_id');
            $ret = $objQuery->insert("dtb_classcategory", $sqlval);
        }
        $objQuery->commit();
        return $ret;
    }

    /* DBへの更新 */
    function lfUpdateClass() {
        $objQuery = new SC_Query();
        // UPDATEする値を作成する。
        $sqlval['name'] = $_POST['name'];
        $sqlval['update_date'] = "Now()";
        $where = "classcategory_id = ?";
        // UPDATEの実行
        $ret = $objQuery->update("dtb_classcategory", $sqlval, $where, array($_POST['classcategory_id']));
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
        $objErr->doFunc(array("分類名", "name", STEXT_LEN), array("EXIST_CHECK","MAX_LENGTH_CHECK"));
        if(!isset($objErr->arrErr['name'])) {
            $objQuery = new SC_Query();
            $where = "class_id = ? AND name = ?";
            $arrRet = $objQuery->select("classcategory_id, name", "dtb_classcategory", $where, array($_GET['class_id'], $_POST['name']));
            // 編集中のレコード以外に同じ名称が存在する場合
            if ($arrRet[0]['classcategory_id'] != $_POST['classcategory_id'] && $arrRet[0]['name'] == $_POST['name']) {
                $objErr->arrErr['name'] = "※ 既に同じ内容の登録が存在します。<br>";
            }
        }
        return $objErr->arrErr;
    }
}
?>
