<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * システム管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id:$
 */
class LC_Page_Admin_System extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();

        $this->list_data    = '';  // テーブルデータ取得用
        $this->tpl_disppage = '';  // 表示中のページ番号
        $this->tpl_strnavi  = '';
        $this->tpl_mainpage = 'system/index.tpl';
        $this->tpl_subnavi  = 'system/subnavi.tpl';
        $this->tpl_mainno   = 'system';
        $this->tpl_subno    = 'index';
        $this->tpl_onload   = 'fnGetRadioChecked();';
        $this->tpl_subtitle = 'メンバー管理';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrAUTHORITY = $masterData->getMasterData('mtb_authority');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView  = new SC_AdminView();
        $objSess  = new SC_Session();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        // ADMIN_ID以外の管理者件数を取得
        $linemax = $this->getMemberCount("del_flg <> 1 AND member_id <> " . ADMIN_ID);

        // ADMIN_ID以外で稼動中の管理者件数を取得
        $this->workmax
            = $this->getMemberCount("work = 1 AND del_flg <> 1 AND member_id <> " . ADMIN_ID);

        // ページ送りの処理
        $pageno = isset($_GET['pageno']) ? $_GET['pageno'] : 1;
        $objNavi = new SC_PageNavi($pageno, $linemax, MEMBER_PMAX, "fnMemberPage", NAVI_PMAX);
        $this->tpl_strnavi  = $objNavi->strnavi;
        $this->tpl_disppage = $objNavi->now_page;
        $this->tpl_pagemax  = $objNavi->max_page;

        // 取得範囲を指定(開始行番号、行数のセット)して管理者データを取得
        $this->list_data = $this->getMemberData($objNavi->start_row);

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

    /**
     * dtb_memberからWHERE句に該当する件数を取得する.
     *
     * @access private
     * @param string $where WHERE句
     * @return integer 件数
     */
     function getMemberCount($where) {
        $objQuery = new SC_Query();
        $table = 'dtb_member';
        return $objQuery->count($table, $where);
     }

    /**
     * 開始行番号、行数を指定して管理者データを取得する.
     *
     * @access private
     * @param integer $startno 開始行番号
     * @return array 管理者データの連想配列
     */
    function getMemberData($startno) {
        $objSql = new SC_SelectSql();
        $objSql->setSelect("SELECT member_id,name,department,login_id,authority,rank,work FROM dtb_member");
        $objSql->setOrder("rank DESC");
        $objSql->setWhere("del_flg <> 1 AND member_id <> ". ADMIN_ID);
        $objSql->setLimitOffset(MEMBER_PMAX, $startno);

        $objQuery = new SC_Query();
        $arrMemberData = $objQuery->getAll($objSql->getSql());

        return $arrMemberData;
     }
}























?>
