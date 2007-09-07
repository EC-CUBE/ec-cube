<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * メール配信履歴 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Mail_History extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'mail/history.tpl';
        $this->tpl_mainno = 'mail';
        $this->tpl_subnavi = 'mail/subnavi.tpl';
        $this->tpl_subno = "history";
        $this->tpl_subtitle = '配信履歴';

    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        //---- ページ初期設定
        $conn = new SC_DBConn();
        $objView = new SC_AdminView();
        $objSess = new SC_Session();
        $objDate = new SC_Date();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        if (!isset($_GET['send_id'])) $_GET['send_id'] = "";
        if (!isset($_GET['mode'])) $_GET['mode'] = "";
        if (!isset($_POST['search_pageno'])) $_POST['search_pageno'] = "";

        // 削除時
        if (SC_Utils_Ex::sfCheckNumLength($_GET['send_id']) && ($_GET['mode']=='delete') ){

            $sql = "UPDATE dtb_send_history SET del_flg = 1 WHERE send_id = ?";
            $conn->query($sql, array($_GET['send_id']) );
            $_SERVER['QUERY_STRING'] = "";
            $this->reload();
        }
        $col = "*";
        $from = "dtb_send_history";

        $where = " del_flg = ?";
        $arrval[] = "0";

        $objQuery = new SC_Query();
        // 行数の取得
        $linemax = $objQuery->count($from, $where, $arrval);
        $this->tpl_linemax = $linemax;              // 何件が該当しました。表示用

        // ページ送りの取得
        $objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, SEARCH_PMAX, "fnNaviSearchPage", NAVI_PMAX);
        $this->tpl_strnavi = $objNavi->strnavi;     // 表示文字列
        $startno = $objNavi->start_row;

        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setlimitoffset(SEARCH_PMAX, $startno);

        // 表示順序
        $order = "start_date DESC, send_id DESC";
        $objQuery->setorder($order);

        // 検索結果の取得
        $this->arrDataList = $objQuery->select($col, $from, $where, $arrval);

        //---- ページ表示
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
}
?>
