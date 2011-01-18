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
 * @version $Id: $
 */
class LC_Page_MyPage_Favorite extends LC_Page {

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
        $this->tpl_subtitle = 'お気に入り一覧';
        $this->tpl_navi = TEMPLATE_REALDIR . 'mypage/navi.tpl';
        $this->tpl_mainno = 'mypage';
        $this->tpl_mypageno = 'favorite';
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
            // マイページトップ顧客情報表示用
            $this->CustomerName1 = $objCustomer->getvalue('name01');
            $this->CustomerName2 = $objCustomer->getvalue('name02');
            $this->CustomerPoint = $objCustomer->getvalue('point');
        }

        // お気に入り削除
        if ($_POST['mode'] == 'delete_favorite') {
            $customer_id = $objCustomer->getValue('customer_id');
            $this->lfDeleteFavoriteProduct($customer_id, $_POST['product_id']);
        }

        // ページ送り用
        if (isset($_POST['pageno'])) {
            $this->tpl_pageno = htmlspecialchars($_POST['pageno'], ENT_QUOTES, CHAR_CODE);
        }

        // FIXME SC_Product クラスを使用した実装
        $col = "alldtl.*";
        $from = "dtb_customer_favorite_products AS dcfp LEFT JOIN vw_products_allclass_detail AS alldtl USING(product_id)";
        
        $where = "dcfp.customer_id = ? AND alldtl.del_flg = 0 AND alldtl.status = 1";
        // 在庫無し商品の非表示
        if (NOSTOCK_HIDDEN === true) {
            $where .= ' AND (alldtl.stock_max >= 1 OR alldtl.stock_unlimited_max = 1)';
        }
        $order = "create_date DESC";

        $arrval = array($objCustomer->getvalue('customer_id'));

        // お気に入りの数を取得
        $linemax = $objQuery->count($from, $where, $arrval);
        $this->tpl_linemax = $linemax;

        // ページ送りの取得
        $objNavi = new SC_PageNavi($this->tpl_pageno, $linemax, SEARCH_PMAX, "fnNaviPage", NAVI_PMAX);
        $this->tpl_strnavi = $objNavi->strnavi;		// 表示文字列
        $startno = $objNavi->start_row;

        // 取得範囲の指定(開始行番号、行数のセット)
        $objQuery->setLimitOffset(SEARCH_PMAX, $startno);
        // 表示順序
        $objQuery->setOrder($order);

        // お気に入りの取得
        $this->arrFavorite = $objQuery->select($col, $from, $where, $arrval);

        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // POST値の取得
        $this->objFormParam->setParam($_POST);

        // 入力情報を渡す
        $this->arrForm = $this->objFormParam->getFormParamList();
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

    // お気に入り商品削除
    function lfDeleteFavoriteProduct($customer_id, $product_id) {
        $objQuery = new SC_Query();
        $count = $objQuery->count("dtb_customer_favorite_products", "customer_id = ? AND product_id = ?", array($customer_id, $product_id));

        if ($count > 0) {
            $objQuery->begin();
            $objQuery->delete('dtb_customer_favorite_products', "customer_id = ? AND product_id = ?", array($customer_id, $product_id));
            $objQuery->commit();
        }
    }
}
?>
