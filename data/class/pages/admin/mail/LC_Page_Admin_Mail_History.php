<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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

require_once CLASS_EX_REALDIR . 'page_extends/admin/LC_Page_Admin_Ex.php';

/**
 * メール配信履歴 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Mail_History extends LC_Page_Admin_Ex
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
        $this->tpl_mainpage = 'mail/history.tpl';
        $this->tpl_mainno = 'mail';
        $this->tpl_subno = 'history';
        $this->tpl_maintitle = 'メルマガ管理';
        $this->tpl_subtitle = '配信履歴';
        $this->tpl_pager = 'pager.tpl';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    public function action()
    {
        switch ($this->getMode()) {
            case 'delete':
                if (SC_Utils_Ex::sfIsInt($_GET['send_id'])) {
                    // 削除時
                    $this->lfDeleteHistory($_GET['send_id']);

                    $this->objDisplay->reload(null, true);
                }
                break;
            default:
                break;
        }

        list($this->tpl_linemax, $this->arrDataList, $this->arrPagenavi) = $this->lfDoSearch($_POST['search_pageno']);
    }

    /**
     * 実行履歴の取得
     *
     * @param  integer $search_pageno 表示したいページ番号
     * @return array(  integer 全体件数, mixed メール配信データ一覧配列, mixed SC_PageNaviオブジェクト)
     */
    public function lfDoSearch($search_pageno = 1)
    {
        // 引数の初期化
        if (SC_Utils_Ex::sfIsInt($search_pageno)===false) {
            $search_pageno = 1;
        }
        //
        $objSelect =& SC_Query_Ex::getSingletonInstance();    // 一覧データ取得用
        $objQuery =& SC_Query_Ex::getSingletonInstance();    // 件数取得用

        // 該当全体件数の取得
        $linemax = $objQuery->count('dtb_send_history', 'del_flg = 0');

        // 一覧データの取得
        $objSelect->setOrder('start_date DESC, send_id DESC');

        $col = '*';
        $col .= ',(SELECT COUNT(*) FROM dtb_send_customer WHERE dtb_send_customer.send_id = dtb_send_history.send_id) AS count_all';
        $col .= ',(SELECT COUNT(*) FROM dtb_send_customer WHERE dtb_send_customer.send_id = dtb_send_history.send_id AND send_flag = 1) AS count_sent';
        $col .= ',(SELECT COUNT(*) FROM dtb_send_customer WHERE dtb_send_customer.send_id = dtb_send_history.send_id AND send_flag = 2) AS count_error';
        $col .= ',(SELECT COUNT(*) FROM dtb_send_customer WHERE dtb_send_customer.send_id = dtb_send_history.send_id AND send_flag IS NULL) AS count_unsent';

        // ページ送りの取得
        $offset = SEARCH_PMAX * ($search_pageno - 1);
        $objSelect->setLimitOffset(SEARCH_PMAX, $offset);
        $arrResult = $objSelect->select($col, 'dtb_send_history', ' del_flg = 0');

        $objNavi = new SC_PageNavi_Ex($search_pageno,
                                    $linemax,
                                    SEARCH_PMAX);

        return array($linemax, $arrResult, $objNavi->arrPagenavi);
    }

    /**
     * 送信履歴の削除
     * @return void
     */
    public function lfDeleteHistory($send_id)
    {
            $objQuery =& SC_Query_Ex::getSingletonInstance();
            $objQuery->update('dtb_send_history',
                              array('del_flg' =>1),
                              'send_id = ?',
                              array($send_id));
    }
}
