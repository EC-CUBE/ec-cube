<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * お届け先編集 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mypage_Delivery extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = TEMPLATE_DIR .'mypage/delivery.tpl';
        $this->tpl_title = "MYページ/お届け先追加･変更";
        $this->tpl_navi = TEMPLATE_DIR . 'mypage/navi.tpl';
        $this->tpl_mainno = 'mypage';
        $this->tpl_mypageno = 'delivery';
        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref= $masterData->getMasterData("mtb_pref",
                            array("pref_id", "pref_name", "rank"));
        $this->tpl_column_num = 1;
        $this->allowClientCache();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_SiteView();
        $objCustomer = new SC_Customer();
        $objQuery = new SC_Query();
        $objConn = new SC_DBConn();

        //ログイン判定
        if(!$objCustomer->isLoginSuccess()) {
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        }else {
            //マイページトップ顧客情報表示用
            $this->CustomerName1 = $objCustomer->getvalue('name01');
            $this->CustomerName2 = $objCustomer->getvalue('name02');
            $this->CustomerPoint = $objCustomer->getvalue('point');
        }

        // レイアウトデザインを取得
        $objLayout = new SC_Helper_PageLayout_Ex();
        $objLayout->sfGetPageLayout($this, false, "mypage/index.php");

        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        //削除
        if($_POST['mode'] == 'delete') {
            //不正アクセス判定
            $flag = $objQuery->count("dtb_other_deliv", "customer_id=? AND other_deliv_id=?", array($objCustomer->getValue('customer_id'), $_POST['other_deliv_id']));
            if($flag > 0) {
                //削除
                $objQuery->delete("dtb_other_deliv", "other_deliv_id=?", array($_POST['other_deliv_id']));
            } else {
                SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
            }
        }

        $this->tpl_pageno = isset($_POST['pageno']) ? $_POST['pageno'] : "";

        $from = "dtb_other_deliv";
        $where = "customer_id=?";
        $arrval = array($objCustomer->getValue('customer_id'));
        $order = "other_deliv_id DESC";

        //お届け先登録件数取得
        $linemax = $objQuery->count($from, $where, $arrval);

        $this->tpl_linemax = $linemax;

        // 表示順序
        $objQuery->setorder($order);

        //別のお届け先情報表示
        $this->arrOtherDeliv = $objQuery->select("*", $from, $where, $arrval);

        //お届け先登録数をテンプレートに渡す
        $objPge->deliv_cnt = count($this->arrOtherDeliv);

        $objView->assignobj($this);
        $objView->display(SITE_FRAME);
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
