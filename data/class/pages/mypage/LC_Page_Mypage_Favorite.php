<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * MyPage のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Mypage.php 16582 2007-10-29 03:06:29Z nanasess $
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
        $this->tpl_mainpage = TEMPLATE_DIR .'mypage/favorite.tpl';
        $this->tpl_title = 'MYページ';
        $this->tpl_subtitle = 'お気に入り一覧';
        $this->tpl_navi = TEMPLATE_DIR . 'mypage/navi.tpl';
        $this->tpl_column_num = 1;
        $this->tpl_mainno = 'mypage';
        $this->tpl_mypageno = 'favorite';
        $this->allowClientCache();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {

        $objView = new SC_SiteView();
        $objQuery = new SC_Query();
        $objCustomer = new SC_Customer();

        // レイアウトデザインを取得
        $objLayout = new SC_Helper_PageLayout_Ex();
        $objLayout->sfGetPageLayout($this, false, "mypage/index.php");

        // ログインチェック
        if(!$objCustomer->isLoginSuccess()) {
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }else {
            //マイページトップ顧客情報表示用
            $this->CustomerName1 = $objCustomer->getvalue('name01');
            $this->CustomerName2 = $objCustomer->getvalue('name02');
            $this->CustomerPoint = $objCustomer->getvalue('point');
        }

        // お気に入り削除
        if ($_POST['mode'] == 'delete_favorite') {
            $customer_id = $objCustomer->getValue('customer_id');
            $this->lfDeleteFavoriteProduct($customer_id, $_POST['product_id']);
        }

        //ページ送り用
        if (isset($_POST['pageno'])) {
            $this->tpl_pageno = htmlspecialchars($_POST['pageno'], ENT_QUOTES, CHAR_CODE);
        }

        $col = "*";
        $from =" (SELECT
                        T2.product_id AS product_id_main,
                        T2.del_flg ,
                        T2.status ,
                        T2.name ,
                        T2.main_list_image ,
                        T1.create_date ,
                        T1.customer_id
                    FROM
                       (SELECT
                            product_id AS product_id_c ,
                            create_date ,
                            customer_id
                        FROM
                           dtb_customer_favorite_products
                        ) AS T1 INNER JOIN dtb_products AS T2 ON T1.product_id_c = T2.product_id
                    ) AS T3 INNER JOIN
                        (SELECT
                            product_id ,
                            MIN(price02) AS price02_min ,
                            MAX(price02) AS price02_max ,
                            MAX(stock) AS stock_max ,
                            MAX(stock_unlimited) AS stock_unlimited_max
                         FROM
                            dtb_products_class
                         GROUP BY
                            product_id
                    ) AS T4 ON T3.product_id_main = T4.product_id";
        $where = "customer_id = ? AND del_flg = 0 AND status = 1";
        // 在庫無し商品の非表示
        if (NOSTOCK_HIDDEN === true) {
            $where .= " AND (stock_max >= 1 OR stock_unlimited_max = 1)";
        }
        $order = "create_date DESC";

        $arrval = array($objCustomer->getvalue('customer_id'));

        //お気に入りの数を取得
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

        //お気に入りの取得
        $this->arrFavorite = $objQuery->select($col, $from, $where, $arrval);

        // パラメータ管理クラス
        $this->objFormParam = new SC_FormParam();
        // POST値の取得
        $this->objFormParam->setParam($_POST);

        // 入力情報を渡す
        $this->arrForm = $this->objFormParam->getFormParamList();
        $objView->assignobj($this);    //$objpage内の全てのテンプレート変数をsmartyに格納
        $objView->display(SITE_FRAME); //パスとテンプレート変数の呼び出し、実行
    }

    /**
     * モバイルページを初期化する.
     *
     * @return void
     */
    function mobileInit() {
        $this->tpl_mainpage = 'mypage/favorite.tpl';
        $this->tpl_title = 'MYページ/お気に入り一覧';
        $this->allowClientCache();
    }

    /**
     * Page のプロセス(モバイル).
     *
     * @return void
     */
    function mobileProcess() {
        $objView = new SC_MobileView();
        $objQuery = new SC_Query();
        $objCustomer = new SC_Customer();
        // クッキー管理クラス
        $objCookie = new SC_Cookie(COOKIE_EXPIRE);
        // パラメータ管理クラス
        $objFormParam = new SC_FormParam();
        // パラメータ情報の初期化
        $this->lfInitParamMobile($objFormParam);
        // POST値の取得
        $objFormParam->setParam($_POST);

        // 携帯端末IDが一致する会員が存在するかどうかをチェックする。
        $this->tpl_valid_phone_id = $objCustomer->checkMobilePhoneId();

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        // ログイン処理
        if($_POST['mode'] == 'login') {
            $objFormParam->toLower('login_email');
            $arrErr = $objFormParam->checkError();
            $arrForm =  $objFormParam->getHashArray();

            // クッキー保存判定
            if ($arrForm['login_memory'] == "1" && $arrForm['login_email'] != "") {
                $objCookie->setCookie('login_email', $_POST['login_email']);
            } else {
                $objCookie->setCookie('login_email', '');
            }

            if (count($arrErr) == 0){
                if($objCustomer->getCustomerDataFromMobilePhoneIdPass($arrForm['login_pass']) ||
                   $objCustomer->getCustomerDataFromEmailPass($arrForm['login_pass'], $arrForm['login_email'], true)) {
                    // ログインが成功した場合は携帯端末IDを保存する。
                    $objCustomer->updateMobilePhoneId();

                    /*
                     * email がモバイルドメインでは無く,
                     * 携帯メールアドレスが登録されていない場合
                     */
                    $objMobile = new SC_Helper_Mobile_Ex();
                    if (!$objMobile->gfIsMobileMailAddress($objCustomer->getValue('email'))) {
                        if (!$objCustomer->hasValue('email_mobile')) {
                            $this->sendRedirect($this->getLocation("../entry/email_mobile.php"), true);
                        }
                    }
                } else {
                    $objQuery = new SC_Query;
                    $where = "(email = ? OR email_mobile = ?) AND status = 1 AND del_flg = 0";
                    $ret = $objQuery->count("dtb_customer", $where, array($arrForm['login_email'], $arrForm['login_email']));

                    if($ret > 0) {
                        SC_Utils_Ex::sfDispSiteError(TEMP_LOGIN_ERROR, "", false, "", true);
                    } else {
                        SC_Utils_Ex::sfDispSiteError(SITE_LOGIN_ERROR, "", false, "", true);
                    }
                }
            }
        }

        /*
         * ログインチェック
         * 携帯メールの登録を必須にする場合は isLoginSuccess(false) にする
         */
        if(!$objCustomer->isLoginSuccess(true)) {
            $this->tpl_mainpage = 'mypage/login.tpl';
            $objView->assignArray($objFormParam->getHashArray());
            if (empty($arrErr)) $arrErr = array();
            $objView->assignArray(array("arrErr" => $arrErr));
        }else {
            //マイページトップ顧客情報表示用
            $this->CustomerName1 = $objCustomer->getvalue('name01');
            $this->CustomerName2 = $objCustomer->getvalue('name02');
        }

        $objView->assignobj($this);       //$objpage内の全てのテンプレート変数をsmartyに格納
        $objView->display(SITE_FRAME);    //パスとテンプレート変数の呼び出し、実行

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
    function lfInitParamMobile(&$objFormParam) {

        $objFormParam->addParam("記憶する", "login_memory", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
        $objFormParam->addParam("メールアドレス", "login_email", MTEXT_LEN, "a", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
        $objFormParam->addParam("パスワード", "login_pass", STEXT_LEN, "a", array("EXIST_CHECK", "MAX_LENGTH_CHECK"));
    }

    // お気に入り商品削除
    function lfDeleteFavoriteProduct($customer_id, $product_id) {
        $objQuery = new SC_Query();
        $objConn = new SC_DbConn();
        $count = $objConn->getOne("SELECT COUNT(*) FROM dtb_customer_favorite_products WHERE customer_id = ? AND product_id = ?", array($customer_id, $product_id));

        if ($count > 0) {
            $where = "customer_id = ? AND product_id = ?";
            $sqlval['customer_id'] = $customer_id;
            $sqlval['product_id'] = $product_id;

            $objQuery->begin();
            $objQuery->delete('dtb_customer_favorite_products', $where, $sqlval);
            $objQuery->commit();
        }
    }



}
?>
