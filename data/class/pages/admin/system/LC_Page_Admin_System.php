<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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
require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * システム管理 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_System extends LC_Page_Admin_Ex {

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
        $this->tpl_mainno   = 'system';
        $this->tpl_subno    = 'index';
        $this->tpl_onload   = 'fnGetRadioChecked();';
        $this->tpl_maintitle = 'システム設定';
        $this->tpl_subtitle = 'メンバー管理';

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrAUTHORITY = $masterData->getMasterData('mtb_authority');
        $this->arrWORK[0]   = '非稼働';
        $this->arrWORK[1]   = '稼働';
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

        // ADMIN_ID以外の管理者件数を取得
        $linemax = $this->getMemberCount('del_flg <> 1 AND member_id <> ' . ADMIN_ID);

        // ADMIN_ID以外で稼動中の管理者件数を取得
        $this->workmax
            = $this->getMemberCount('work = 1 AND del_flg <> 1 AND member_id <> ' . ADMIN_ID);

        // ページ送りの処理 $_GET['pageno']が信頼しうる値かどうかチェックする。
        $pageno = $this->lfCheckPageNo($_GET['pageno']);

        $objNavi = new SC_PageNavi_Ex($pageno, $linemax, MEMBER_PMAX, 'fnMemberPage', NAVI_PMAX);
        $this->tpl_strnavi  = $objNavi->strnavi;
        $this->tpl_disppage = $objNavi->now_page;
        $this->tpl_pagemax  = $objNavi->max_page;

        // 取得範囲を指定(開始行番号、行数のセット)して管理者データを取得
        $this->list_data = $this->getMemberData($objNavi->start_row);
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
        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $table = 'dtb_member';
        return $objQuery->count($table, $where);
    }

    /**
     * 開始行番号, 行数を指定して管理者データを取得する.
     *
     * @access private
     * @param integer $startno 開始行番号
     * @return array 管理者データの連想配列
     */
    function getMemberData($startno) {
        $objSql = new SC_SelectSql_Ex();
        $objSql->setSelect('SELECT member_id,name,department,login_id,authority,rank,work FROM dtb_member');
        $objSql->setOrder('rank DESC');
        $objSql->setWhere('del_flg <> 1 AND member_id <> '. ADMIN_ID);
        $objSql->setLimitOffset(MEMBER_PMAX, $startno);

        $objQuery =& SC_Query_Ex::getSingletonInstance();
        $arrMemberData = $objQuery->getAll($objSql->getSql());

        return $arrMemberData;
    }

    /**
     * ページ番号が信頼しうる値かチェックする.
     *
     * @access private
     * @param integer  $pageno ページの番号（$_GETから入ってきた値）
     * @return integer $clean_pageno チェック後のページの番号
     */
    function lfCheckPageNo($pageno) {

        $clean_pageno = '';

        // $pagenoが0以上の整数かチェック
        if (SC_Utils_Ex::sfIsInt($pageno) && $pageno > 0) {
            $clean_pageno = $pageno;
        }

        // 例外は全て1とする
        else {
            $clean_pageno = 1;
        }

        return $clean_pageno;
    }
}
