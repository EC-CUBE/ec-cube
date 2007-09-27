<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

// {{{ requires
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * 購入履歴 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Mypage_Hisotory extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = TEMPLATE_DIR . 'mypage/history.tpl';
        $this->tpl_title = "MYページ/購入履歴詳細";
        $this->tpl_navi = TEMPLATE_DIR . 'mypage/navi.tpl';
        $this->tpl_column_num = 1;
        $this->tpl_mainno = 'mypage';
        $this->tpl_mypageno = 'index';
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
        $objDb = new SC_Helper_DB_Ex();

        // レイアウトデザインを取得
        $objLayout = new SC_Helper_PageLayout_Ex();
        $objLayout->sfGetPageLayout($this, false, "mypage/index.php");

        //不正アクセス判定
        $from = "dtb_order";
        $where = "del_flg = 0 AND customer_id = ? AND order_id = ? ";
        $arrval = array($objCustomer->getValue('customer_id'), $_POST['order_id']);
        //DBに情報があるか判定
        $cnt = $objQuery->count($from, $where, $arrval);
        //ログインしていない、またはDBに情報が無い場合
        if (!$objCustomer->isLoginSuccess() || $cnt == 0){
            SC_Utils_Ex::sfDispSiteError(CUSTOMER_ERROR);
        } else {
            //受注詳細データの取得
            $this->arrDisp = $this->lfGetOrderData($_POST['order_id']);
            // 支払い方法の取得
            $this->arrPayment = $objDb->sfGetIDValueList("dtb_payment", "payment_id", "payment_method");
            // 配送時間の取得
            $arrRet = $objDb->sfGetDelivTime($this->arrDisp['payment_id']);
            $this->arrDelivTime = SC_Utils_Ex::sfArrKeyValue($arrRet, 'time_id', 'deliv_time');

            //マイページトップ顧客情報表示用
            $this->CustomerName1 = $objCustomer->getvalue('name01');
            $this->CustomerName2 = $objCustomer->getvalue('name02');
            $this->CustomerPoint = $objCustomer->getvalue('point');
        }

        $masterData = new SC_DB_MasterData_Ex();
        $this->arrPref = $masterData->getMasterData("mtb_pref",
                                 array("pref_id", "pref_name", "rank"));
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

    //受注詳細データの取得
    function lfGetOrderData($order_id) {
        //受注番号が数字であれば
        if(SC_Utils_Ex::sfIsInt($order_id)) {
            // DBから受注情報を読み込む
            $objQuery = new SC_Query();
            $col = "order_id, create_date, payment_id, subtotal, tax, use_point, add_point, discount, ";
            $col .= "deliv_fee, charge, payment_total, deliv_name01, deliv_name02, deliv_kana01, deliv_kana02, ";
            $col .= "deliv_zip01, deliv_zip02, deliv_pref, deliv_addr01, deliv_addr02, deliv_tel01, deliv_tel02, deliv_tel03, deliv_time_id, deliv_date ";
            $from = "dtb_order";
            $where = "order_id = ?";
            $arrRet = $objQuery->select($col, $from, $where, array($order_id));
            $arrOrder = $arrRet[0];
            // 受注詳細データの取得
            $arrRet = $this->lfGetOrderDetail($order_id);
            $arrOrderDetail = SC_Utils_Ex::sfSwapArray($arrRet);
            $arrData = array_merge($arrOrder, $arrOrderDetail);
        }
        return $arrData;
    }

    // 受注詳細データの取得
    function lfGetOrderDetail($order_id) {
        $objQuery = new SC_Query();
        $col = "product_id, product_code, product_name, classcategory_name1, classcategory_name2, price, quantity, point_rate";
        $where = "order_id = ?";
        $objQuery->setorder("classcategory_id1, classcategory_id2");
        $arrRet = $objQuery->select($col, "dtb_order_detail", $where, array($order_id));
        return $arrRet;
    }
}
?>
