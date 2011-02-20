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
        $this->tpl_pager = 'pager.tpl';
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
        $objSess = new SC_Session();
        // 認証可否の判定
        SC_Utils_Ex::sfIsSuccess($objSess);

        switch ($this->getMode()) {
        case 'delete':
            // 削除時
            if (SC_Utils_Ex::sfIsInt($_GET['send_id'])) {
                $sql = "UPDATE dtb_send_history SET del_flg = 1 WHERE send_id = ?";
                $objQuery->query($sql, array($_GET['send_id']) );
                $_SERVER['QUERY_STRING'] = "";
                $this->objDisplay->reload();
            }
            break;
        default:
            break;
        }

        // 行数の取得
        $linemax = $objQuery->count($from, $where, $arrval);

        // ページ送りの取得
        $objNavi = new SC_PageNavi($_POST['search_pageno'], $linemax, SEARCH_PMAX, "fnNaviSearchPage", NAVI_PMAX);
        $this->tpl_strnavi = $objNavi->strnavi;     // 表示文字列
        $startno = $objNavi->start_row;

        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setLimitOffset(SEARCH_PMAX, $startno);

        // 検索結果の取得
        $this->arrDataList = $this->lfGetMailHistory();
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
     * 実行履歴の取得
     *
     * @return array( integer 全体件数, mixed メール配信データ一覧配列, mixed SC_PageNaviオブジェクト)
     */
    function lfGetMailHistory() {
        $objQuery =& SC_Query::getSingletonInstance();
        $objQuery->setOrder("start_date DESC, send_id DESC");
        
        $col = "*";
        $col .= ",(SELECT COUNT(*) FROM dtb_send_customer WHERE dtb_send_customer.send_id = dtb_send_history.send_id) AS count_all";
        $col .= ",(SELECT COUNT(*) FROM dtb_send_customer WHERE dtb_send_customer.send_id = dtb_send_history.send_id AND send_flag = 1) AS count_sent";
        $col .= ",(SELECT COUNT(*) FROM dtb_send_customer WHERE dtb_send_customer.send_id = dtb_send_history.send_id AND send_flag = 2) AS count_error";
        $col .= ",(SELECT COUNT(*) FROM dtb_send_customer WHERE dtb_send_customer.send_id = dtb_send_history.send_id AND send_flag IS NULL) AS count_unsent";
        
        $arrResult = $objQuery->select($col, "dtb_send_history", " del_flg = 0");
        return $arrResult;
        
        /*
        $page_rows = $arrParam['page_rows'];
        if(SC_Utils_Ex::sfIsInt($page_rows)) {
            $page_max = $page_rows;
        }else{
            $page_max = SEARCH_PMAX;
        }
        $disp_pageno = $arrParam['search_pageno'];
        if($disp_pageno == 0) {
            $disp_pageno = 1;
        }
        $offset = $page_max * ($disp_pageno - 1);
        $objSelect->setLimitOffset($page_max, $offset);
        $arrData = $objQuery->getAll($objSelect->getList(), $objSelect->arrVal);
        
        // 該当全体件数の取得
        $linemax = $objQuery->getOne($objSelect->getListCount(), $objSelect->arrVal);
        // ページ送りの取得
        $objNavi = new SC_PageNavi($arrParam['search_pageno'],
                                    $linemax,
                                    $page_max,
                                    "fnCustomerPage",
                                    NAVI_PMAX);
        return array($linemax, $arrData, $objNavi);
    }	*/
    
}
?>
