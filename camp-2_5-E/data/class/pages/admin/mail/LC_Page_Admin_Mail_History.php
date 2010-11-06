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
require_once(CLASS_PATH . "pages/admin/LC_Page_Admin.php");

/**
 * メール配信履歴 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Mail_History extends LC_Page_Admin {

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
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        // ページ初期設定
        $objQuery = new SC_Query();
        $objSess = new SC_Session();
        $objDate = new SC_Date();

        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        if (!isset($_GET['send_id'])) $_GET['send_id'] = "";
        if (!isset($_GET['mode'])) $_GET['mode'] = "";
        if (!isset($_POST['search_pageno'])) $_POST['search_pageno'] = "";

        // 削除時
        if (SC_Utils_Ex::sfCheckNumLength($_GET['send_id']) && ($_GET['mode']=='delete')) {

            $sql = "UPDATE dtb_send_history SET del_flg = 1 WHERE send_id = ?";
            $objQuery->query($sql, array($_GET['send_id']) );
            $_SERVER['QUERY_STRING'] = "";
            $this->objDisplay->reload();
        }
        $from = "dtb_send_history";

        $where = " del_flg = ?";
        $arrval[] = "0";

        // 行数の取得
        $linemax = $objQuery->count($from, $where, $arrval);
        $this->tpl_linemax = $linemax;              // 何件が該当しました。表示用

        // ページ送りの取得
        $objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, SEARCH_PMAX, "fnNaviSearchPage", NAVI_PMAX);
        $this->tpl_strnavi = $objNavi->strnavi;     // 表示文字列
        $startno = $objNavi->start_row;

        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setLimitOffset(SEARCH_PMAX, $startno);

        // 表示順序
        $order = "start_date DESC, send_id DESC";
        $objQuery->setOrder($order);

        $col = "*";
        $col .= ",(SELECT COUNT(*) FROM dtb_send_customer WHERE dtb_send_customer.send_id = dtb_send_history.send_id) AS count_all";
        $col .= ",(SELECT COUNT(*) FROM dtb_send_customer WHERE dtb_send_customer.send_id = dtb_send_history.send_id AND send_flag = 1) AS count_sent";
        $col .= ",(SELECT COUNT(*) FROM dtb_send_customer WHERE dtb_send_customer.send_id = dtb_send_history.send_id AND send_flag = 2) AS count_error";
        $col .= ",(SELECT COUNT(*) FROM dtb_send_customer WHERE dtb_send_customer.send_id = dtb_send_history.send_id AND send_flag IS NULL) AS count_unsent";

        // 検索結果の取得
        $this->arrDataList = $objQuery->select($col, $from, $where, $arrval);
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
