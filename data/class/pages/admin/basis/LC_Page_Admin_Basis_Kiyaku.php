<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 会員規約設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Basis_Kiyaku extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'basis/kiyaku.tpl';
        $this->tpl_subnavi = 'basis/subnavi.tpl';
        $this->tpl_subno = 'kiyaku';
        $this->tpl_subtitle = '会員規約登録';
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
                if($_POST['kiyaku_id'] == "") {
                    $this->lfInsertClass($this->arrForm);	// 新規作成
                } else {
                    $this->lfUpdateClass($this->arrForm);	// 既存編集
                }
                // 再表示
                $this->reload();
            } else {
                // POSTデータを引き継ぐ
                $this->tpl_kiyaku_id = $_POST['kiyaku_id'];
            }
            break;
        // 削除
        case 'delete':
            $objDb->sfDeleteRankRecord("dtb_kiyaku", "kiyaku_id", $_POST['kiyaku_id'], "", true);
            // 再表示
            $this->reload();
            break;
        // 編集前処理
        case 'pre_edit':
            // 編集項目をDBより取得する。
            $where = "kiyaku_id = ?";
            $arrRet = $objQuery->select("kiyaku_text, kiyaku_title", "dtb_kiyaku", $where, array($_POST['kiyaku_id']));
            // 入力項目にカテゴリ名を入力する。
            $this->arrForm['kiyaku_title'] = $arrRet[0]['kiyaku_title'];
            $this->arrForm['kiyaku_text'] = $arrRet[0]['kiyaku_text'];
            // POSTデータを引き継ぐ
            $this->tpl_kiyaku_id = $_POST['kiyaku_id'];
        break;
        case 'down':
            $objDb->sfRankDown("dtb_kiyaku", "kiyaku_id", $_POST['kiyaku_id']);
            // 再表示
            $this->reload();
            break;
        case 'up':
            $objDb->sfRankUp("dtb_kiyaku", "kiyaku_id", $_POST['kiyaku_id']);
            // 再表示
            $this->reload();
            break;
        default:
            break;
        }

        // 規格の読込
        $where = "del_flg <> 1";
        $objQuery->setorder("rank DESC");
        $this->arrKiyaku = $objQuery->select("kiyaku_title, kiyaku_text, kiyaku_id", "dtb_kiyaku", $where);

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
        $sqlval['kiyaku_title'] = $arrData['kiyaku_title'];
        $sqlval['kiyaku_text'] = $arrData['kiyaku_text'];
        $sqlval['creator_id'] = $_SESSION['member_id'];
        $sqlval['rank'] = $objQuery->max("dtb_kiyaku", "rank") + 1;
        $sqlval['update_date'] = "Now()";
        $sqlval['create_date'] = "Now()";
        // INSERTの実行
        $ret = $objQuery->insert("dtb_kiyaku", $sqlval);
        return $ret;
    }

    /* DBへの更新 */
    function lfUpdateClass($arrData) {
        $objQuery = new SC_Query();
        // UPDATEする値を作成する。
        $sqlval['kiyaku_title'] = $arrData['kiyaku_title'];
        $sqlval['kiyaku_text'] = $arrData['kiyaku_text'];
        $sqlval['update_date'] = "Now()";
        $where = "kiyaku_id = ?";
        // UPDATEの実行
        $ret = $objQuery->update("dtb_kiyaku", $sqlval, $where, array($_POST['kiyaku_id']));
        return $ret;
    }

    /* 取得文字列の変換 */
    function lfConvertParam($array) {
        // 文字変換
        $arrConvList['kiyaku_title'] = "KVa";
        $arrConvList['kiyaku_text'] = "KVa";

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
        $objErr->doFunc(array("規約タイトル", "kiyaku_title", SMTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("規約内容", "kiyaku_text", MLTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
        if(!isset($objErr->arrErr['name'])) {
            $objQuery = new SC_Query();
            $arrRet = $objQuery->select("kiyaku_id, kiyaku_title", "dtb_kiyaku", "del_flg = 0 AND kiyaku_title = ?", array($_POST['kiyaku_title']));
            // 編集中のレコード以外に同じ名称が存在する場合
            if ($arrRet[0]['kiyaku_id'] != $_POST['kiyaku_id'] && $arrRet[0]['kiyaku_title'] == $_POST['kiyaku_title']) {
                $objErr->arrErr['name'] = "※ 既に同じ内容の登録が存在します。<br>";
            }
        }
        return $objErr->arrErr;
    }
}
?>
