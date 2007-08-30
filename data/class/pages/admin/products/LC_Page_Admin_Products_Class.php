<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 規格管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Products_Class extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'products/class.tpl';
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
                if($_POST['class_id'] == "") {
                    $this->lfInsertClass($this->arrForm);	// 新規作成
                } else {
                    $this->lfUpdateClass($this->arrForm);	// 既存編集
                }
                // 再表示
                $this->reload();
            } else {
                // POSTデータを引き継ぐ
                $this->tpl_class_id = $_POST['class_id'];
            }
            break;
            // 削除
        case 'delete':
            $objDb->sfDeleteRankRecord("dtb_class", "class_id", $_POST['class_id'], "", true);
            $objQuery = new SC_Query();
            $objQuery->delete("dtb_classcategory", "class_id = ?", $_POST['class_id']);
            // 再表示
            $this->reload();
            break;
            // 編集前処理
        case 'pre_edit':
            // 編集項目をDBより取得する。
            $where = "class_id = ?";
            $class_name = $objQuery->get("dtb_class", "name", $where, array($_POST['class_id']));
            // 入力項目にカテゴリ名を入力する。
            $this->arrForm['name'] = $class_name;
            // POSTデータを引き継ぐ
            $this->tpl_class_id = $_POST['class_id'];
            break;
        case 'down':
            $objDb->sfRankDown("dtb_class", "class_id", $_POST['class_id']);
            // 再表示
            $this->reload();
            break;
        case 'up':
            $objDb->sfRankUp("dtb_class", "class_id", $_POST['class_id']);
            // 再表示
            $this->reload();
            break;
        default:
            break;
        }

        // 規格の読込
        $where = "del_flg <> 1";
        $objQuery->setorder("rank DESC");
        $this->arrClass = $objQuery->select("name, class_id", "dtb_class", $where);
        $this->arrClassCatCount = SC_Utils_Ex::sfGetClassCatCount();

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
        $sqlval['creator_id'] = $_SESSION['member_id'];
        $sqlval['rank'] = $objQuery->max("dtb_class", "rank") + 1;
        $sqlval['create_date'] = "now()";
        $sqlval['update_date'] = "now()";
        // INSERTの実行
        $ret = $objQuery->insert("dtb_class", $sqlval);

        return $ret;
    }

    /* DBへの更新 */
    function lfUpdateClass($arrData) {
        $objQuery = new SC_Query();
        // UPDATEする値を作成する。
        $sqlval['name'] = $arrData['name'];
        $sqlval['update_date'] = "Now()";
        $where = "class_id = ?";
        // UPDATEの実行
        $ret = $objQuery->update("dtb_class", $sqlval, $where, array($arrData['class_id']));
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
        $objErr->doFunc(array("規格名", "name", STEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));

        if(!isset($objErr->arrErr['name'])) {
            $objQuery = new SC_Query();
            $arrRet = $objQuery->select("class_id, name", "dtb_class", "del_flg = 0 AND name = ?", array($_POST['name']));
            // 編集中のレコード以外に同じ名称が存在する場合
            if ($arrRet[0]['class_id'] != $_POST['class_id'] && $arrRet[0]['name'] == $_POST['name']) {
                $objErr->arrErr['name'] = "※ 既に同じ内容の登録が存在します。<br>";
            }
        }
        return $objErr->arrErr;
    }
}
?>
