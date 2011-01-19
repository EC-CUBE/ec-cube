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
require_once(CLASS_REALDIR . "pages/LC_Page.php");

/**
 * MyPage のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_MyPage extends LC_Page {

    // {{{ properties

    /** ページナンバー */
    var $tpl_pageno;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_title = 'MYページ';
        if (Net_UserAgent_Mobile::isMobile() === true){
            $this->tpl_subtitle = 'MYページ';
        } else {
            $this->tpl_subtitle = '購入履歴一覧';
        }
        $this->tpl_navi = TEMPLATE_REALDIR . 'mypage/navi.tpl';
        $this->tpl_mainno = 'mypage';
        $this->tpl_mypageno = 'index';
        $this->httpCacheControl('nocache');
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        parent::process();
        $this->action();
        $this->sendResponse();
    }
    
    /**
     * Page のAction.
     *
     * @return void
     */
    function action() {

        $objQuery = new SC_Query();
        $objCustomer = new SC_Customer();
        
        // 退会判定用情報の取得
        $this->tpl_login = $objCustomer->isLoginSuccess(true);

        // ログインチェック
        if(!$objCustomer->isLoginSuccess(true)) {
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }else {
            //マイページトップ顧客情報表示用
            $this->CustomerName1 = $objCustomer->getvalue('name01');
            $this->CustomerName2 = $objCustomer->getvalue('name02');
            $this->CustomerPoint = $objCustomer->getvalue('point');
        }

        //ページ送り用
        if (isset($_POST['pageno'])) {
            $this->tpl_pageno = htmlspecialchars($_POST['pageno'], ENT_QUOTES, CHAR_CODE);
        }

        $col = "order_id, create_date, payment_id, payment_total";
        $from = "dtb_order";
        $where = "del_flg = 0 AND customer_id=?";
        $arrval = array($objCustomer->getvalue('customer_id'));
        $order = "order_id DESC";

        $linemax = $objQuery->count($from, $where, $arrval);
        $this->tpl_linemax = $linemax;

        if (Net_UserAgent_Mobile::isMobile() === true){
            define ("HISTORY_NUM", 5);  // TODO
            $pageNo = isset($_GET['pageno']) ? (int) $_GET['pageno'] : 0; // TODO

            // ページ送りの取得
            // next
            if ($pageNo + HISTORY_NUM < $linemax) {
                $next = "<a href='?pageno=" . ($pageNo + HISTORY_NUM) . "'>次へ→</a>";
            } else {
                $next = "";
            }

            // previous
            if ($pageNo - HISTORY_NUM > 0) {
                $previous = "<a href='?pageno=" . ($pageNo - HISTORY_NUM) . "'>←前へ</a>";
            } elseif ($pageNo == 0) {
                $previous = "";
            } else {
                $previous = "<a href='?pageno=0'>←前へ</a>";
            }

            // bar
            if ($next != '' && $previous != '') {
                $bar = " | ";
            } else {
                $bar = "";
            }

            $this->tpl_strnavi = $previous . $bar . $next;

            // 取得範囲の指定(開始行番号、行数のセット)
            $objQuery->setLimitOffset(HISTORY_NUM, $pageNo);
        } else {
            // ページ送りの取得
            $objNavi = new SC_PageNavi($this->tpl_pageno, $linemax, SEARCH_PMAX, "fnNaviPage", NAVI_PMAX);
            $this->tpl_strnavi = $objNavi->strnavi;		// 表示文字列
            $startno = $objNavi->start_row;
            
            // 取得範囲の指定(開始行番号、行数のセット)
            $objQuery->setLimitOffset(SEARCH_PMAX, $startno);
        }
        
        // 表示順序
        $objQuery->setOrder($order);

        //購入履歴の取得
        $this->arrOrder = $objQuery->select($col, $from, $where, $arrval);

        // 支払い方法の取得
        $objDb = new SC_Helper_DB_Ex();
        $this->arrPayment = $objDb->sfGetIDValueList("dtb_payment", "payment_id", "payment_method");

    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    //エラーチェック

    function lfErrorCheck() {
        $objErr = new SC_CheckError();
        $objErr->doFunc(array("メールアドレス", "login_email", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK","EMAIL_CHECK","MAX_LENGTH_CHECK"));
        $objErr->dofunc(array("パスワード", "login_password", PASSWORD_LEN2), array("EXIST_CHECK","ALNUM_CHECK"));
        return $objErr->arrErr;
    }

    /* パラメータ情報の初期化 */
    function lfInitParam(&$objFormParam) {

        $objFormParam->addParam("記憶する", "login_memory", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("メールアドレス", "login_email", MTEXT_LEN, "a", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("パスワード", "login_pass", STEXT_LEN, "a", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
    }

}
?>
